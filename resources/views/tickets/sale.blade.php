<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta - {{ $sale['code'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }
        
        .ticket-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
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
        
        .ticket-info {
            margin-bottom: 15px;
        }
        
        .ticket-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .products-table th,
        .products-table td {
            text-align: left;
            padding: 3px 2px;
            border-bottom: 1px dashed #ccc;
        }
        
        .products-table th {
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        
        .product-name {
            max-width: 120px;
            word-wrap: break-word;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            font-size: 10px;
        }
        
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        @media print {
            body {
                max-width: none;
                margin: 0;
                padding: 5px;
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
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Imprimir</button>
    
    <div class="ticket-header">
        <div class="company-name">FERRETERÍA</div>
        <div class="company-info">Sistema de Gestión</div>
        <div class="company-info">Ticket de Venta</div>
    </div>
    
    <div class="ticket-info">
        <div>
            <span><strong>Código:</strong></span>
            <span>{{ $sale['code'] }}</span>
        </div>
        <div>
            <span><strong>Fecha:</strong></span>
            <span>{{ $sale['created_at'] }}</span>
        </div>
        <div>
            <span><strong>Cliente:</strong></span>
            <span>{{ $sale['customer_name'] }}</span>
        </div>
        <div>
            <span><strong>Vendedor:</strong></span>
            <span>{{ $sale['user'] }}</span>
        </div>
    </div>
    
    <div class="dashed-line"></div>
    
    <table class="products-table">
        <thead>
            <tr>
                <th class="product-name">Producto</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Precio</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale['products'] as $product)
            <tr>
                <td class="product-name">{{ $product['name'] }}</td>
                <td class="text-center">{{ $product['quantity'] }}</td>
                <td class="text-right">S/ {{ number_format($product['price'], 2) }}</td>
                <td class="text-right">S/ {{ number_format($product['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="total-section">
        <div class="total-line">
            <span>TOTAL:</span>
            <span>S/ {{ number_format($sale['total'], 2) }}</span>
        </div>
    </div>
    
    <div class="footer">
        <div>¡Gracias por su compra!</div>
        <div>{{ date('d/m/Y H:i:s') }}</div>
    </div>
    
    <script>
        // Auto-imprimir si se solicita
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                window.print();
            };
        }
        
        // Cerrar ventana después de imprimir
        window.onafterprint = function() {
            if (window.location.search.includes('print=1')) {
                window.close();
            }
        };
    </script>
</body>
</html> 