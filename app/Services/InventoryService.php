<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\InventoryStockin;
use App\Models\InventoryStockUsage;
use App\Models\StockInCart;
use App\Models\StockInDetail;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function stockIn(InventoryStockin $stockIn, int $itemId, float $quantity, float $buyingPrice): void
    {
        DB::transaction(function () use ($stockIn, $itemId, $quantity, $buyingPrice): void {
            $total = $quantity * $buyingPrice;

            $stockIn->forceFill(['total' => $total])->save();

            $detail = new StockInDetail;
            $detail->forceFill([
                'stockInId' => $stockIn->getKey(),
                'itemId' => $itemId,
                'quantity' => $quantity,
                'buying_price' => $buyingPrice,
                'businessId' => $stockIn->businessId,
                'userId' => $stockIn->userId,
            ])->save();

            $item = InventoryItem::query()->where('businessId', $stockIn->businessId)->findOrFail($itemId);
            $item->forceFill([
                'quantity' => (float) $item->quantity + $quantity,
                'last_buying_price' => $buyingPrice,
                'buying_price' => $buyingPrice,
            ])->save();

            $movement = new InventoryMovement;
            $movement->forceFill([
                'date' => $stockIn->date,
                'itemId' => $itemId,
                'quantity_in' => $quantity,
                'quantity_out' => 0,
                'description' => 'Stock in',
                'type' => InventoryMovementType::Stocking->value,
                'tid' => $stockIn->getKey(),
                'businessId' => $stockIn->businessId,
                'userId' => $stockIn->userId,
            ])->save();
        });
    }

    public function stockInFromCart(InventoryStockin $stockIn): void
    {
        DB::transaction(function () use ($stockIn): void {
            $cartItems = StockInCart::query()
                ->where('businessId', $stockIn->businessId)
                ->where('userId', $stockIn->userId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new RuntimeException('Add at least one item to the stock in cart.');
            }

            $total = $cartItems->sum(fn (StockInCart $cartItem): float => (float) $cartItem->quantity * (float) $cartItem->buying_price);

            $stockIn->forceFill(['total' => $total])->save();

            foreach ($cartItems as $cartItem) {
                $detail = new StockInDetail;
                $detail->forceFill([
                    'stockInId' => $stockIn->getKey(),
                    'itemId' => $cartItem->itemId,
                    'quantity' => $cartItem->quantity,
                    'buying_price' => $cartItem->buying_price,
                    'businessId' => $stockIn->businessId,
                    'userId' => $stockIn->userId,
                ])->save();

                $item = InventoryItem::query()->where('businessId', $stockIn->businessId)->findOrFail($cartItem->itemId);
                $item->forceFill([
                    'quantity' => (float) $item->quantity + (float) $cartItem->quantity,
                    'last_buying_price' => $cartItem->buying_price,
                    'buying_price' => $cartItem->buying_price,
                ])->save();

                $movement = new InventoryMovement;
                $movement->forceFill([
                    'date' => $stockIn->date,
                    'itemId' => $cartItem->itemId,
                    'quantity_in' => $cartItem->quantity,
                    'quantity_out' => 0,
                    'description' => 'Stock in',
                    'type' => InventoryMovementType::Stocking->value,
                    'tid' => $stockIn->getKey(),
                    'businessId' => $stockIn->businessId,
                    'userId' => $stockIn->userId,
                ])->save();
            }

            StockInCart::query()
                ->where('businessId', $stockIn->businessId)
                ->where('userId', $stockIn->userId)
                ->delete();
        });
    }

    public function deleteStockIn(InventoryStockin $stockIn): void
    {
        DB::transaction(function () use ($stockIn): void {
            foreach ($stockIn->details as $detail) {
                $item = InventoryItem::query()->where('businessId', $stockIn->businessId)->findOrFail($detail->itemId);
                $item->forceFill(['quantity' => max(0, (float) $item->quantity - (float) $detail->quantity)])->save();
            }

            InventoryMovement::query()
                ->where('businessId', $stockIn->businessId)
                ->where('type', InventoryMovementType::Stocking->value)
                ->where('tid', $stockIn->getKey())
                ->delete();

            $stockIn->details()->delete();
            $stockIn->delete();
        });
    }

    public function useStock(InventoryStockUsage $usage): void
    {
        DB::transaction(function () use ($usage): void {
            $item = InventoryItem::query()->where('businessId', $usage->businessId)->findOrFail($usage->itemId);

            if ((float) $item->quantity < (float) $usage->quantity) {
                throw new RuntimeException('The selected item does not have enough stock.');
            }

            $usage->save();

            $item->forceFill(['quantity' => (float) $item->quantity - (float) $usage->quantity])->save();

            $movement = new InventoryMovement;
            $movement->forceFill([
                'date' => $usage->date,
                'itemId' => $usage->itemId,
                'quantity_in' => 0,
                'quantity_out' => $usage->quantity,
                'description' => $usage->description ?: 'Stock usage',
                'type' => InventoryMovementType::Usage->value,
                'tid' => $usage->getKey(),
                'businessId' => $usage->businessId,
                'userId' => $usage->userId,
            ])->save();
        });
    }

    public function deleteUsage(InventoryStockUsage $usage): void
    {
        DB::transaction(function () use ($usage): void {
            $item = InventoryItem::query()->where('businessId', $usage->businessId)->findOrFail($usage->itemId);
            $item->forceFill(['quantity' => (float) $item->quantity + (float) $usage->quantity])->save();

            InventoryMovement::query()
                ->where('businessId', $usage->businessId)
                ->where('type', InventoryMovementType::Usage->value)
                ->where('tid', $usage->getKey())
                ->delete();

            $usage->delete();
        });
    }
}
