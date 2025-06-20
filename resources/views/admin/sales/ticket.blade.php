<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta - {{ $sale->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        
        .ticket {
            width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .sale-info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .sale-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .products {
            margin-bottom: 15px;
        }
        
        .products-header {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .product-item {
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .product-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .product-details {
            display: flex;
            justify-content: space-between;
        }
        
        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
        }
        
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 8px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10px;
        }
        
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 5mm;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Imprimir Ticket</button>
    
    <div class="ticket">
        <div class="header">
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-info">{{ $company['address'] }}</div>
            <div class="company-info">Tel: {{ $company['phone'] }}</div>
            <div class="company-info">{{ $company['email'] }}</div>
        </div>
        
        <div class="sale-info">
            <div>
                <span>Ticket:</span>
                <span>{{ $sale->code }}</span>
            </div>
            <div>
                <span>Fecha:</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
                <span>Vendedor:</span>
                <span>{{ $sale->user->name ?? 'Sistema' }}</span>
            </div>
            <div>
                <span>Estado:</span>
                <span>{{ ucfirst($sale->status) }}</span>
            </div>
        </div>
        
        <div class="products">
            <div class="products-header">
                PRODUCTOS
            </div>
            
            @foreach($sale->products as $product)
            <div class="product-item">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-details">
                    <span>{{ $product->pivot->quantity }} x S/ {{ number_format($product->pivot->price, 2) }}</span>
                    <span>S/ {{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="totals">
            @php
                $subtotal = $sale->products->sum(function($product) {
                    return $product->pivot->quantity * $product->pivot->price;
                });
                $igv = $subtotal * 0.18;
                $total = $sale->total;
            @endphp
            
            <div class="total-line">
                <span>Subtotal:</span>
                <span>S/ {{ number_format($subtotal - $igv, 2) }}</span>
            </div>
            <div class="total-line">
                <span>IGV (18%):</span>
                <span>S/ {{ number_format($igv, 2) }}</span>
            </div>
            <div class="total-line total-final">
                <span>TOTAL:</span>
                <span>S/ {{ number_format($total, 2) }}</span>
            </div>
        </div>
        
        <div class="footer">
            <div>¡Gracias por su compra!</div>
            <div>{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
    
    <script>
        // Auto-imprimir al cargar la página
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html> 