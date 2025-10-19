-- Original Database Schema (with issues for analysis)
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `address` text,
  `total_amount` varchar(50) DEFAULT NULL,
  `items` text,
  `status` varchar(50) DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for testing
INSERT INTO `orders` (`date`, `customer_name`, `customer_email`, `address`, `total_amount`, `items`, `status`) VALUES
('2024-01-15 10:30:00', 'John Doe', 'john@example.com', '123 Main St, New York, NY 10001', '299.99', 'Product A, Product B', 'completed'),
('2024-01-16 14:20:00', 'Jane Smith', 'jane@example.com', '456 Oak Ave, Los Angeles, CA 90001', '149.50', 'Product C', 'pending'),
('2024-02-10 09:15:00', 'Bob Johnson', 'bob@example.com', '789 Pine Rd, Chicago, IL 60601', '599.00', 'Product D, Product E, Product F', 'completed'),
('2024-02-20 16:45:00', 'Alice Brown', 'alice@example.com', '321 Elm St, Houston, TX 77001', '89.99', 'Product G', 'cancelled'),
('2024-03-05 11:30:00', 'Charlie Wilson', 'charlie@example.com', '654 Maple Dr, Phoenix, AZ 85001', '1299.00', 'Product H', 'completed'),
('2024-03-12 13:00:00', 'Diana Davis', 'diana@example.com', '987 Cedar Ln, Philadelphia, PA 19101', '349.99', 'Product I, Product J', 'pending'),
('2024-04-01 10:00:00', 'Edward Miller', 'edward@example.com', '147 Birch St, San Antonio, TX 78201', '199.99', 'Product K', 'completed'),
('2024-04-15 15:30:00', 'Fiona Garcia', 'fiona@example.com', '258 Spruce Ave, San Diego, CA 92101', '449.00', 'Product L, Product M', 'completed'),
('2024-05-03 12:45:00', 'George Martinez', 'george@example.com', '369 Ash Rd, Dallas, TX 75201', '79.99', 'Product N', 'pending'),
('2024-05-20 09:20:00', 'Helen Rodriguez', 'helen@example.com', '741 Willow Dr, San Jose, CA 95101', '899.99', 'Product O, Product P, Product Q', 'completed'),
('2024-06-08 14:15:00', 'Ian Hernandez', 'ian@example.com', '852 Poplar Ln, Austin, TX 78701', '159.50', 'Product R', 'pending'),
('2024-06-25 11:50:00', 'Julia Lopez', 'julia@example.com', '963 Fir St, Jacksonville, FL 32099', '729.00', 'Product S, Product T', 'completed'),
('2024-07-10 16:00:00', 'Kevin Gonzalez', 'kevin@example.com', '159 Hickory Ave, Fort Worth, TX 76101', '399.99', 'Product U', 'completed'),
('2024-07-22 10:30:00', 'Laura Wilson', 'laura@example.com', '357 Walnut Rd, Columbus, OH 43085', '249.99', 'Product V, Product W', 'pending'),
('2024-08-05 13:45:00', 'Michael Anderson', 'michael@example.com', '468 Chestnut Dr, Charlotte, NC 28201', '1499.00', 'Product X', 'completed'),
('2024-08-18 09:00:00', 'Nancy Thomas', 'nancy@example.com', '579 Beech Ln, San Francisco, CA 94101', '89.00', 'Product Y', 'cancelled'),
('2024-09-02 15:15:00', 'Oscar Taylor', 'oscar@example.com', '681 Cypress St, Indianapolis, IN 46201', '549.99', 'Product Z, Product AA', 'completed'),
('2024-09-15 11:20:00', 'Patricia Moore', 'patricia@example.com', '792 Magnolia Ave, Seattle, WA 98101', '299.00', 'Product BB', 'pending'),
('2024-10-01 14:30:00', 'Quincy Jackson', 'quincy@example.com', '813 Dogwood Rd, Denver, CO 80201', '179.99', 'Product CC, Product DD', 'completed'),
('2024-10-12 10:45:00', 'Rachel Martin', 'rachel@example.com', '924 Redwood Dr, Boston, MA 02101', '999.99', 'Product EE', 'completed');
