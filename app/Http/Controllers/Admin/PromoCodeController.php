<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::latest()->paginate(20);
        return view('admin.promo-codes.index', compact('promoCodes'));
    }
    
    public function create()
    {
        return view('admin.promo-codes.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:promo_codes,code|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'required|date|after:today',
        ]);
        
        $validated['code'] = strtoupper($validated['code']);
        $validated['used_count'] = 0;
        
        PromoCode::create($validated);
        
        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code created successfully!');
    }
    
    public function edit(PromoCode $promoCode)
    {
        return view('admin.promo-codes.edit', compact('promoCode'));
    }
    
    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,' . $promoCode->id,
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'required|date',
        ]);
        
        $validated['code'] = strtoupper($validated['code']);
        
        $promoCode->update($validated);
        
        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code updated successfully!');
    }
    
    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();
        
        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code deleted successfully!');
    }
}
