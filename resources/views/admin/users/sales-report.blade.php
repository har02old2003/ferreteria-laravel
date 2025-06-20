<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            background: #f8f9fa;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .company-name {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 2px;
        }
        
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
        }
        
        .report-date {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .content {
            padding: 30px;
        }
        
        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .user-info h3 {
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-info h3::before {
            content: '👤';
            font-size: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            background: rgba(255,255,255,0.1);
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid rgba(255,255,255,0.5);
        }
        
        .info-label {
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: 500;
        }
        
        .summary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }
        
        .summary h3 {
            font-size: 18px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .summary h3::before {
            content: '📊';
            font-size: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        
        .summary-item {
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .summary-value {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .summary-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sales-section {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .sales-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sales-header::before {
            content: '🛒';
            font-size: 18px;
        }
        
        .sales-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .sales-table th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 15px 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .sales-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: top;
        }
        
        .sales-table tr:hover {
            background: #f8f9fa;
        }
        
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .products-list {
            max-width: 300px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .product-item {
            background: #f8f9fa;
            padding: 4px 8px;
            margin: 2px 0;
            border-radius: 4px;
            border-left: 3px solid #FF6B35;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #666;
        }
        
        .footer-logo {
            font-size: 16px;
            font-weight: bold;
            color: #FF6B35;
            margin-bottom: 5px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.6);
        }
        
        .no-sales {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-sales-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-sales h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #495057;
        }
        
        @media print {
            .print-button {
                display: none;
            }
            
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .header {
                background: #FF6B35 !important;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Imprimir Reporte</button>
    
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="company-name">FERRECHINCHA</div>
                <div class="company-tagline">Tu ferretería de confianza</div>
                <div class="report-title">Reporte de Ventas por Usuario</div>
                <div class="report-date">Generado el: {{ $generatedAt }}</div>
            </div>
        </div>
        
        <div class="content">
            <div class="user-info">
                <h3>Información del Usuario</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nombre Completo</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Correo Electrónico</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Rol en el Sistema</div>
                        <div class="info-value">{{ $user->role === 'admin' ? 'Administrador' : 'Empleado' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fecha de Registro</div>
                        <div class="info-value">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="summary">
                <h3>Resumen de Ventas</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-value">{{ $totalSales }}</div>
                        <div class="summary-label">Total de Ventas</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">S/ {{ number_format($totalAmount, 2) }}</div>
                        <div class="summary-label">Monto Total</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">{{ $sales->where('status', 'completed')->count() }}</div>
                        <div class="summary-label">Ventas Completadas</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">S/ {{ $totalSales > 0 ? number_format($totalAmount / $totalSales, 2) : '0.00' }}</div>
                        <div class="summary-label">Promedio por Venta</div>
                    </div>
                </div>
            </div>
            
            @if($sales->count() > 0)
                <div class="sales-section">
                    <div class="sales-header">
                        Detalle de Ventas Realizadas
                    </div>
                    <table class="sales-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Código</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Productos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td><strong>{{ $sale->code ?? 'V' . str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td><strong>S/ {{ number_format($sale->total, 2) }}</strong></td>
                                <td>
                                    <span class="status status-{{ $sale->status }}">
                                        {{ $sale->status === 'completed' ? 'Completada' : ($sale->status === 'pending' ? 'Pendiente' : 'Cancelada') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="products-list">
                                        @foreach($sale->items as $item)
                                            <div class="product-item">
                                                {{ $item->quantity }}x {{ $item->product ? $item->product->name : 'Producto eliminado' }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="no-sales">
                    <div class="no-sales-icon">📋</div>
                    <h3>No hay ventas registradas</h3>
                    <p>Este usuario aún no ha realizado ninguna venta en el sistema.</p>
                </div>
            @endif
            
            <div class="footer">
                <div class="footer-logo">FERRECHINCHA</div>
                <p>Reporte generado automáticamente por el sistema de gestión</p>
                <p><strong>{{ $generatedAt }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html> 