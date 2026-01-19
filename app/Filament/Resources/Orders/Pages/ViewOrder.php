<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load items if not already loaded
        $order = $this->getRecord();
        
        // Calculate subtotal from items
        $subtotal = 0;
        if ($order->items) {
            foreach ($order->items as $item) {
                $subtotal += $item->price * $item->quantity;
            }
        }
        
        $data['subtotal'] = $subtotal;
        $data['admin_fee'] = $subtotal * 0.025;
        
        return $data;
    }
    
    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
    
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
    
    public function getContentTabLabel(): ?string
    {
        return null;
    }
    
    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'maxContentWidth' => 'full',
        ]);
    }
}
