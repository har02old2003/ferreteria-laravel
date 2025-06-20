-- ============================================
-- DATOS DE PRUEBA PARA FERRECHINCHA
-- Sistema de Ferretería Completo
-- ============================================

-- Limpiar datos existentes (opcional)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE sale_items;
TRUNCATE TABLE sales;
TRUNCATE TABLE products;
TRUNCATE TABLE suppliers;
TRUNCATE TABLE brands;
TRUNCATE TABLE categories;
TRUNCATE TABLE clients;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 1. USUARIOS DEL SISTEMA
-- ============================================

INSERT INTO users (id, name, email, email_verified_at, password, role, created_at, updated_at) VALUES
(1, 'Administrador Principal', 'admin@ferrechincha.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
(2, 'Carlos Mendoza', 'carlos@ferrechincha.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW()),
(3, 'María García', 'maria@ferrechincha.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW()),
(4, 'Roberto Silva', 'roberto@ferrechincha.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW());

-- ============================================
-- 2. CATEGORÍAS DE PRODUCTOS
-- ============================================

INSERT INTO categories (id, name, description, is_active, created_at, updated_at) VALUES
(1, 'Herramientas Manuales', 'Martillos, destornilladores, llaves, alicates y herramientas de mano', 1, NOW(), NOW()),
(2, 'Herramientas Eléctricas', 'Taladros, sierras eléctricas, lijadoras y herramientas con motor', 1, NOW(), NOW()),
(3, 'Materiales de Construcción', 'Cemento, arena, ladrillos y materiales básicos', 1, NOW(), NOW()),
(4, 'Fontanería', 'Tuberías, llaves, grifos y accesorios de agua', 1, NOW(), NOW()),
(5, 'Electricidad', 'Cables, interruptores, enchufes y material eléctrico', 1, NOW(), NOW()),
(6, 'Ferretería General', 'Tornillos, clavos, tuercas, arandelas y pequeña ferretería', 1, NOW(), NOW()),
(7, 'Pintura y Acabados', 'Pinturas, brochas, rodillos y materiales de acabado', 1, NOW(), NOW()),
(8, 'Jardinería', 'Herramientas de jardín, mangueras y accesorios', 1, NOW(), NOW()),
(9, 'Seguridad Industrial', 'Cascos, guantes, gafas y equipos de protección', 1, NOW(), NOW()),
(10, 'Adhesivos y Selladores', 'Pegamentos, silicones, cintas y selladores', 1, NOW(), NOW());

-- ============================================
-- 3. MARCAS
-- ============================================

INSERT INTO brands (id, name, description, is_active, created_at, updated_at) VALUES
(1, 'STANLEY', 'Herramientas profesionales de calidad superior', 1, NOW(), NOW()),
(2, 'BLACK & DECKER', 'Herramientas eléctricas para el hogar y profesionales', 1, NOW(), NOW()),
(3, 'DEWALT', 'Herramientas industriales de alta resistencia', 1, NOW(), NOW()),
(4, 'MAKITA', 'Herramientas eléctricas japonesas de precisión', 1, NOW(), NOW()),
(5, 'BOSCH', 'Tecnología alemana en herramientas', 1, NOW(), NOW()),
(6, 'TRUPER', 'Herramientas mexicanas de calidad', 1, NOW(), NOW()),
(7, 'MILWAUKEE', 'Herramientas profesionales heavy duty', 1, NOW(), NOW()),
(8, 'IRWIN', 'Herramientas de medición y corte', 1, NOW(), NOW()),
(9, 'SURTEK', 'Herramientas y accesorios diversos', 1, NOW(), NOW()),
(10, 'PRETUL', 'Herramientas económicas de buena calidad', 1, NOW(), NOW()),
(11, 'PAVCO', 'Tuberías y accesorios de fontanería', 1, NOW(), NOW()),
(12, 'TIGRE', 'Sistemas de tuberías y conexiones', 1, NOW(), NOW()),
(13, 'LEGRAND', 'Material eléctrico y automatización', 1, NOW(), NOW()),
(14, 'SCHNEIDER', 'Equipos eléctricos industriales', 1, NOW(), NOW()),
(15, 'COMEX', 'Pinturas y recubrimientos', 1, NOW(), NOW());

-- ============================================
-- 4. PROVEEDORES
-- ============================================

INSERT INTO suppliers (id, name, contact_person, phone, email, address, is_active, created_at, updated_at) VALUES
(1, 'Distribuidora FERREMAX S.A.C.', 'Juan Pérez Rodríguez', '01-234-5678', 'ventas@ferremax.com', 'Av. Industrial 1234, Lima', 1, NOW(), NOW()),
(2, 'HERRAMIENTAS DEL PERÚ E.I.R.L.', 'Ana María López', '01-345-6789', 'pedidos@herramientasperu.com', 'Jr. Comercio 567, Cercado de Lima', 1, NOW(), NOW()),
(3, 'CONSTRUMAX DISTRIBUCIONES S.A.', 'Carlos Mendoza Silva', '01-456-7890', 'compras@construmax.pe', 'Av. Argentina 890, Callao', 1, NOW(), NOW()),
(4, 'ELÉCTRICOS MODERNOS S.R.L.', 'Patricia Vargas', '01-567-8901', 'contacto@electricosmodernos.com', 'Av. Brasil 1122, Magdalena', 1, NOW(), NOW()),
(5, 'PINTURAS Y ACABADOS LIMA S.A.C.', 'Roberto García', '01-678-9012', 'ventas@pinturasacabados.pe', 'Av. Colonial 1456, Pueblo Libre', 1, NOW(), NOW()),
(6, 'FONTANERÍA INTEGRAL E.I.R.L.', 'María Elena Torres', '01-789-0123', 'pedidos@fontaneriaintegral.com', 'Jr. Huancavelica 789, Lima Centro', 1, NOW(), NOW()),
(7, 'SEGURIDAD INDUSTRIAL PERÚ S.A.', 'José Luis Ramírez', '01-890-1234', 'info@seguridadindustrial.pe', 'Av. Javier Prado 2345, San Isidro', 1, NOW(), NOW());

-- ============================================
-- 5. PRODUCTOS
-- ============================================

-- Herramientas Manuales
INSERT INTO products (id, name, description, sku, price, stock, minimum_stock, category_id, brand_id, supplier_id, is_active, created_at, updated_at) VALUES
(1, 'Martillo de Uña 16 oz', 'Martillo con mango de fibra de vidrio, cabeza forjada', 'MAR-STAN-16', 45.90, 25, 5, 1, 1, 1, 1, NOW(), NOW()),
(2, 'Destornillador Phillips #2', 'Destornillador punta Phillips, mango ergonómico', 'DES-STAN-PH2', 12.50, 50, 10, 1, 1, 1, 1, NOW(), NOW()),
(3, 'Alicate Universal 8"', 'Alicate multiuso con aislamiento eléctrico', 'ALI-TRUP-8', 28.75, 30, 5, 1, 6, 2, 1, NOW(), NOW()),
(4, 'Llave Inglesa 10"', 'Llave ajustable cromada, apertura máxima 25mm', 'LLA-TRUP-10', 35.20, 20, 5, 1, 6, 2, 1, NOW(), NOW()),
(5, 'Juego de Llaves Allen', 'Set de 9 llaves hexagonales métricas', 'JLL-STAN-9P', 22.90, 15, 3, 1, 1, 1, 1, NOW(), NOW()),

-- Herramientas Eléctricas
(6, 'Taladro Percutor 1/2" 650W', 'Taladro con función percutora, velocidad variable', 'TAL-BD-650', 189.90, 12, 3, 2, 2, 2, 1, NOW(), NOW()),
(7, 'Sierra Circular 7 1/4"', 'Sierra circular 1400W con disco de 184mm', 'SIE-DEW-7', 245.50, 8, 2, 2, 3, 2, 1, NOW(), NOW()),
(8, 'Lijadora Orbital 200W', 'Lijadora con base cuadrada, sistema anti-polvo', 'LIJ-BOS-200', 125.75, 10, 2, 2, 5, 2, 1, NOW(), NOW()),
(9, 'Amoladora Angular 4 1/2"', 'Esmeriladora 750W con disco de corte incluido', 'AMO-MAK-4', 156.30, 15, 3, 2, 4, 2, 1, NOW(), NOW()),
(10, 'Atornillador Inalámbrico 12V', 'Atornillador con batería de litio y cargador', 'ATO-MIL-12V', 98.60, 18, 4, 2, 7, 2, 1, NOW(), NOW()),

-- Materiales de Construcción
(11, 'Cemento Portland Tipo I - 42.5kg', 'Cemento para construcción general', 'CEM-PORT-42', 24.80, 100, 20, 3, 9, 3, 1, NOW(), NOW()),
(12, 'Ladrillo King Kong 18 huecos', 'Ladrillo de arcilla cocida para muros', 'LAD-KK-18H', 0.85, 500, 100, 3, 9, 3, 1, NOW(), NOW()),
(13, 'Arena Gruesa m3', 'Arena para concreto y mezclas', 'ARE-GRU-M3', 45.00, 25, 5, 3, 9, 3, 1, NOW(), NOW()),
(14, 'Fierro Corrugado 1/2" x 9m', 'Varilla de acero para construcción', 'FIE-COR-12', 28.50, 80, 15, 3, 9, 3, 1, NOW(), NOW()),

-- Fontanería
(15, 'Tubería PVC 4" x 3m', 'Tubo PVC para desagüe, presión estándar', 'TUB-PAV-4', 18.90, 40, 8, 4, 11, 6, 1, NOW(), NOW()),
(16, 'Codo PVC 90° - 2"', 'Codo de 90 grados para tubería de 2 pulgadas', 'COD-PAV-2', 3.25, 100, 20, 4, 11, 6, 1, NOW(), NOW()),
(17, 'Llave de Paso 1/2"', 'Válvula esférica de bronce con palanca', 'LLV-TIG-12', 15.75, 35, 8, 4, 12, 6, 1, NOW(), NOW()),
(18, 'Grifo Lavatorio Económico', 'Grifo monomando para lavabo', 'GRI-TIG-LAV', 45.90, 20, 5, 4, 12, 6, 1, NOW(), NOW()),

-- Electricidad
(19, 'Cable THW 12 AWG x 100m', 'Cable sólido para instalaciones eléctricas', 'CAB-LEG-12', 85.60, 25, 5, 5, 13, 4, 1, NOW(), NOW()),
(20, 'Interruptor Simple', 'Interruptor de una vía, 15A 250V', 'INT-LEG-SIM', 8.90, 60, 12, 5, 13, 4, 1, NOW(), NOW()),
(21, 'Tomacorriente Doble', 'Tomacorriente polarizado con tierra', 'TOM-LEG-DOB', 12.50, 45, 10, 5, 13, 4, 1, NOW(), NOW()),
(22, 'Tablero Eléctrico 6 Polos', 'Caja de distribución para 6 interruptores', 'TAB-SCH-6P', 45.80, 15, 3, 5, 14, 4, 1, NOW(), NOW()),

-- Ferretería General
(23, 'Tornillo Autorroscante 1" x 8', 'Tornillo punta aguda, cabeza phillips', 'TOR-AUT-1x8', 0.15, 1000, 200, 6, 9, 1, 1, NOW(), NOW()),
(24, 'Clavo de Acero 3"', 'Clavo común para carpintería', 'CLA-ACE-3', 0.08, 2000, 400, 6, 9, 1, 1, NOW(), NOW()),
(25, 'Tuerca Hexagonal 1/4"', 'Tuerca galvanizada rosca métrica', 'TUE-HEX-14', 0.25, 500, 100, 6, 9, 1, 1, NOW(), NOW()),
(26, 'Arandela Plana 1/4"', 'Arandela galvanizada para tornillo 1/4"', 'ARA-PLA-14', 0.12, 800, 150, 6, 9, 1, 1, NOW(), NOW()),

-- Pintura y Acabados
(27, 'Pintura Látex Blanco 4L', 'Pintura base agua para interiores', 'PIN-COM-BL4', 65.90, 30, 6, 7, 15, 5, 1, NOW(), NOW()),
(28, 'Brocha 3" Cerda Natural', 'Brocha de calidad para pintura', 'BRO-3-CER', 18.50, 25, 5, 7, 9, 5, 1, NOW(), NOW()),
(29, 'Rodillo 9" + Bandeja', 'Kit de rodillo con bandeja plástica', 'ROD-9-KIT', 22.75, 20, 4, 7, 9, 5, 1, NOW(), NOW()),
(30, 'Thinner Acrílico 1L', 'Diluyente para pinturas base agua', 'THI-ACR-1L', 12.90, 40, 8, 7, 15, 5, 1, NOW(), NOW()),

-- Jardinería
(31, 'Manguera de Jardín 1/2" x 15m', 'Manguera flexible con conexiones', 'MAN-JAR-15', 35.80, 20, 4, 8, 9, 2, 1, NOW(), NOW()),
(32, 'Pala Punta Cuadrada', 'Pala de acero con mango de madera', 'PAL-CUA-ACE', 28.90, 15, 3, 8, 6, 2, 1, NOW(), NOW()),
(33, 'Rastrillo 14 Dientes', 'Rastrillo metálico para jardín', 'RAS-14D-MET', 24.50, 12, 3, 8, 6, 2, 1, NOW(), NOW()),

-- Seguridad Industrial
(34, 'Casco de Seguridad Blanco', 'Casco HDPE con arnés ajustable', 'CAS-SEG-BLA', 25.90, 30, 6, 9, 9, 7, 1, NOW(), NOW()),
(35, 'Guantes de Cuero', 'Guantes de carnaza para trabajo pesado', 'GUA-CUE-CAR', 15.75, 40, 8, 9, 9, 7, 1, NOW(), NOW()),
(36, 'Lentes de Seguridad', 'Gafas protectoras transparentes', 'LEN-SEG-TRA', 8.90, 50, 10, 9, 9, 7, 1, NOW(), NOW()),

-- Adhesivos y Selladores
(37, 'Silicona Transparente 280ml', 'Sellador de silicona multiuso', 'SIL-TRA-280', 8.50, 60, 12, 10, 9, 1, 1, NOW(), NOW()),
(38, 'Pegamento PVC 118ml', 'Adhesivo para tuberías PVC', 'PEG-PVC-118', 12.90, 45, 9, 10, 11, 6, 1, NOW(), NOW()),
(39, 'Cinta Aislante Negra', 'Cinta eléctrica PVC 19mm x 20m', 'CIN-AIS-NEG', 3.50, 80, 16, 10, 13, 4, 1, NOW(), NOW()),
(40, 'Teflón 1/2" x 10m', 'Cinta de teflón para roscas', 'TEF-12-10M', 2.80, 100, 20, 10, 12, 6, 1, NOW(), NOW());

-- ============================================
-- 6. CLIENTES
-- ============================================

INSERT INTO clients (id, dni, first_name, last_name, full_name, phone, email, address, status, created_at, updated_at) VALUES
(1, '12345678', 'Juan Carlos', 'Pérez García', 'Juan Carlos Pérez García', '987654321', 'juan.perez@email.com', 'Av. Los Olivos 123, San Martín de Porres', 'active', NOW(), NOW()),
(2, '87654321', 'María Elena', 'Rodríguez López', 'María Elena Rodríguez López', '976543210', 'maria.rodriguez@email.com', 'Jr. Las Flores 456, Los Olivos', 'active', NOW(), NOW()),
(3, '55667788', 'Carlos Alberto', 'Mendoza Silva', 'Carlos Alberto Mendoza Silva', '965432109', 'carlos.mendoza@email.com', 'Av. Universitaria 789, Comas', 'active', NOW(), NOW()),
(4, '11223344', 'Ana Sofía', 'Torres Vargas', 'Ana Sofía Torres Vargas', '954321098', 'ana.torres@email.com', 'Calle Real 321, Independencia', 'active', NOW(), NOW()),
(5, '99887766', 'Roberto Luis', 'García Flores', 'Roberto Luis García Flores', '943210987', 'roberto.garcia@email.com', 'Av. Túpac Amaru 654, Carabayllo', 'active', NOW(), NOW()),
(6, '44556677', 'Patricia Isabel', 'Morales Díaz', 'Patricia Isabel Morales Díaz', '932109876', 'patricia.morales@email.com', 'Jr. Progreso 987, Puente Piedra', 'active', NOW(), NOW()),
(7, '33445566', 'Luis Fernando', 'Ramírez Castro', 'Luis Fernando Ramírez Castro', '921098765', 'luis.ramirez@email.com', 'Av. Industrial 147, Callao', 'active', NOW(), NOW()),
(8, '77889900', 'Carmen Rosa', 'Vásquez Huamán', 'Carmen Rosa Vásquez Huamán', '910987654', 'carmen.vasquez@email.com', 'Calle Los Pinos 258, Villa El Salvador', 'active', NOW(), NOW());

-- ============================================
-- 7. VENTAS DE EJEMPLO
-- ============================================

-- Venta 1: Cliente Juan Carlos - Herramientas básicas
INSERT INTO sales (id, code, user_id, client_id, customer_name, total, status, created_at, updated_at) VALUES
(1, 'VTA-2024-0001', 2, 1, 'Juan Carlos Pérez García', 127.15, 'completed', '2024-01-15 10:30:00', '2024-01-15 10:30:00');

INSERT INTO sale_items (sale_id, product_id, quantity, price, created_at, updated_at) VALUES
(1, 1, 1, 45.90, '2024-01-15 10:30:00', '2024-01-15 10:30:00'),
(1, 2, 2, 12.50, '2024-01-15 10:30:00', '2024-01-15 10:30:00'),
(1, 3, 1, 28.75, '2024-01-15 10:30:00', '2024-01-15 10:30:00'),
(1, 27, 1, 65.90, '2024-01-15 10:30:00', '2024-01-15 10:30:00');

-- Venta 2: Cliente María Elena - Material eléctrico
INSERT INTO sales (id, code, user_id, client_id, customer_name, total, status, created_at, updated_at) VALUES
(2, 'VTA-2024-0002', 3, 2, 'María Elena Rodríguez López', 198.70, 'completed', '2024-01-16 14:20:00', '2024-01-16 14:20:00');

INSERT INTO sale_items (sale_id, product_id, quantity, price, created_at, updated_at) VALUES
(2, 19, 1, 85.60, '2024-01-16 14:20:00', '2024-01-16 14:20:00'),
(2, 20, 5, 8.90, '2024-01-16 14:20:00', '2024-01-16 14:20:00'),
(2, 21, 3, 12.50, '2024-01-16 14:20:00', '2024-01-16 14:20:00'),
(2, 22, 1, 45.80, '2024-01-16 14:20:00', '2024-01-16 14:20:00');

-- Venta 3: Cliente Carlos - Herramientas eléctricas
INSERT INTO sales (id, code, user_id, client_id, customer_name, total, status, created_at, updated_at) VALUES
(3, 'VTA-2024-0003', 2, 3, 'Carlos Alberto Mendoza Silva', 435.40, 'completed', '2024-01-17 09:15:00', '2024-01-17 09:15:00');

INSERT INTO sale_items (sale_id, product_id, quantity, price, created_at, updated_at) VALUES
(3, 6, 1, 189.90, '2024-01-17 09:15:00', '2024-01-17 09:15:00'),
(3, 7, 1, 245.50, '2024-01-17 09:15:00', '2024-01-17 09:15:00');

-- Venta 4: Cliente Ana - Fontanería
INSERT INTO sales (id, code, user_id, client_id, customer_name, total, status, created_at, updated_at) VALUES
(4, 'VTA-2024-0004', 4, 4, 'Ana Sofía Torres Vargas', 83.90, 'completed', '2024-01-18 16:45:00', '2024-01-18 16:45:00');

INSERT INTO sale_items (sale_id, product_id, quantity, price, created_at, updated_at) VALUES
(4, 15, 2, 18.90, '2024-01-18 16:45:00', '2024-01-18 16:45:00'),
(4, 16, 4, 3.25, '2024-01-18 16:45:00', '2024-01-18 16:45:00'),
(4, 17, 1, 15.75, '2024-01-18 16:45:00', '2024-01-18 16:45:00'),
(4, 18, 1, 45.90, '2024-01-18 16:45:00', '2024-01-18 16:45:00');

-- Venta 5: Cliente Roberto - Construcción
INSERT INTO sales (id, code, user_id, client_id, customer_name, total, status, created_at, updated_at) VALUES
(5, 'VTA-2024-0005', 3, 5, 'Roberto Luis García Flores', 168.30, 'completed', '2024-01-19 11:30:00', '2024-01-19 11:30:00');

INSERT INTO sale_items (sale_id, product_id, quantity, price, created_at, updated_at) VALUES
(5, 11, 5, 24.80, '2024-01-19 11:30:00', '2024-01-19 11:30:00'),
(5, 14, 2, 28.50, '2024-01-19 11:30:00', '2024-01-19 11:30:00');

-- ============================================
-- ACTUALIZAR STOCK DESPUÉS DE LAS VENTAS
-- ============================================

UPDATE products SET stock = stock - 1 WHERE id = 1;  -- Martillo
UPDATE products SET stock = stock - 2 WHERE id = 2;  -- Destornillador
UPDATE products SET stock = stock - 1 WHERE id = 3;  -- Alicate
UPDATE products SET stock = stock - 1 WHERE id = 27; -- Pintura

UPDATE products SET stock = stock - 1 WHERE id = 19; -- Cable
UPDATE products SET stock = stock - 5 WHERE id = 20; -- Interruptor
UPDATE products SET stock = stock - 3 WHERE id = 21; -- Tomacorriente
UPDATE products SET stock = stock - 1 WHERE id = 22; -- Tablero

UPDATE products SET stock = stock - 1 WHERE id = 6;  -- Taladro
UPDATE products SET stock = stock - 1 WHERE id = 7;  -- Sierra

UPDATE products SET stock = stock - 2 WHERE id = 15; -- Tubería
UPDATE products SET stock = stock - 4 WHERE id = 16; -- Codo
UPDATE products SET stock = stock - 1 WHERE id = 17; -- Llave
UPDATE products SET stock = stock - 1 WHERE id = 18; -- Grifo

UPDATE products SET stock = stock - 5 WHERE id = 11; -- Cemento
UPDATE products SET stock = stock - 2 WHERE id = 14; -- Fierro

-- ============================================
-- INFORMACIÓN ADICIONAL
-- ============================================

-- Contraseña para todos los usuarios: password
-- Los emails de usuarios son funcionales para testing
-- Los DNIs de clientes son válidos para testing
-- Los códigos SKU siguen un patrón lógico: CATEGORIA-MARCA-ESPECIFICACION
-- Los precios están en soles peruanos (PEN)
-- El stock mínimo está configurado para alertas automáticas

-- ============================================
-- CONSULTAS ÚTILES PARA VERIFICACIÓN
-- ============================================

-- Ver resumen de productos por categoría:
-- SELECT c.name as categoria, COUNT(p.id) as total_productos, SUM(p.stock) as stock_total
-- FROM categories c 
-- LEFT JOIN products p ON c.id = p.category_id 
-- WHERE c.is_active = 1 
-- GROUP BY c.id, c.name;

-- Ver ventas del día:
-- SELECT s.code, s.customer_name, s.total, s.created_at
-- FROM sales s 
-- WHERE DATE(s.created_at) = CURDATE()
-- ORDER BY s.created_at DESC;

-- Ver productos con stock bajo:
-- SELECT p.name, p.stock, p.minimum_stock, c.name as categoria
-- FROM products p
-- JOIN categories c ON p.category_id = c.id
-- WHERE p.stock <= p.minimum_stock
-- ORDER BY p.stock ASC;