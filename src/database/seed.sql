-- Seed data for Cartly. Run AFTER schema.sql.
USE cartly;
-- Users (passwords are PHP password_hash() of "password123")
-- Hash below is bcrypt for "password123"
INSERT INTO users (username, full_name, email, phone, password, role, status) VALUES
  ('admin',    'Admin User',    'admin@cartly.test',    '0100000001', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'admin',    'active'),
  ('merchant', 'Green Grocer',  'merchant@cartly.test', '0100000002', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'merchant', 'active'),
  ('customer', 'Jane Customer', 'customer@cartly.test', '0100000003', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'customer', 'active'),
  ('merchant2','Daily Mart',    'mart@cartly.test',     '0100000004', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'merchant', 'active'),
  ('merchant3','Fresh Basket',  'fresh@cartly.test',    '0100000005', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'merchant', 'active'),
  ('merchant4','Farm Lane',     'farm@cartly.test',     '0100000006', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'merchant', 'active'),
  ('sarah',    'Sarah Lim',     'sarah@cartly.test',    '0100000007', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'customer', 'active'),
  ('amir',     'Amir Hakim',    'amir@cartly.test',     '0100000008', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'customer', 'active'),
  ('lina',     'Lina Tan',      'lina@cartly.test',     '0100000009', '$2y$12$q5PjR41J5hXFLbtf9Sz2XO9Ikzo99XQANWmcx04n8QPZYHmlLoVne', 'customer', 'active');
INSERT INTO stores (user_id, store_name, store_description, contact_email, contact_phone, store_address, opening_time, closing_time, store_status, rating) VALUES
  (2, 'Green Valley Grocers', 'Fresh produce and pantry staples.', 'merchant@cartly.test', '0100000002', '12 Market Street', '08:00:00', '20:00:00', 'approved', 4.5),
  (4, 'Daily Mart',           'Everyday groceries.',              'mart@cartly.test',     '0100000004', '88 High Road',     '09:00:00', '22:00:00', 'approved', 4.2),
  (5, 'Fresh Basket',         'Fresh fruit, vegetables, and basics.', 'fresh@cartly.test', '0100000005', '19 Orchard Lane', '08:00:00', '21:00:00', 'approved', 4.7),
  (6, 'Farm Lane Market',     'Local farm produce and pantry goods.', 'farm@cartly.test',  '0100000006', '77 Rural Road',   '07:30:00', '20:30:00', 'approved', 4.4);
INSERT INTO categories (category_name, category_icon, status) VALUES
  ('Vegetables', 'veg',    'active'),
  ('Meat',       'meat',   'active'),
  ('Dairy',      'dairy',  'active'),
  ('Grains',     'grain',  'active'),
  ('Pantry',     'pantry', 'active'),
  ('Frozen',     'frozen', 'active'),
  ('Beverages',  'drink',  'active');
INSERT INTO ingredients (ingredient_name, base_unit) VALUES
  ('Rice',           'g'),
  ('Chicken Breast', 'g'),
  ('Egg',            'pcs'),
  ('Tomato',         'g'),
  ('Onion',          'g'),
  ('Garlic',         'g'),
  ('Salt',           'g'),
  ('Olive Oil',      'ml'),
  ('Milk',           'ml'),
  ('Butter',         'g'),
  ('Potato',         'g'),
  ('Carrot',         'g'),
  ('Coffee',         'g');
INSERT INTO products (store_id, category_id, ingredient_id, product_name, description, price, stock_quantity, package_quantity, package_unit, rating, status) VALUES
  (1, 4, 1, 'Jasmine Rice 1kg',      'Premium fragrant rice.',  6.50, 50, 1000, 'g',   4.6, 'active'),
  (2, 4, 1, 'Basmati Rice 500g',     'Long grain basmati.',     5.20, 40, 500,  'g',   4.3, 'active'),
  (1, 2, 2, 'Chicken Breast 500g',   'Skinless chicken breast.',12.00,30, 500,  'g',   4.4, 'active'),
  (2, 2, 2, 'Chicken Breast 1kg',    'Bulk pack.',              22.00,20, 1000, 'g',   4.1, 'active'),
  (1, 3, 3, 'Free Range Eggs (10)',  'Pack of 10 eggs.',        7.80, 60, 10,   'pcs', 4.7, 'active'),
  (1, 1, 4, 'Tomatoes 500g',         'Vine ripened.',           4.20, 25, 500,  'g',   4.2, 'active'),
  (2, 1, 5, 'Brown Onions 1kg',      'Cooking onions.',         3.90, 35, 1000, 'g',   4.0, 'active'),
  (1, 5, 8, 'Olive Oil 500ml',       'Extra virgin.',          18.00, 15, 500,  'ml',  4.8, 'active'),
  (3, 4, 1, 'Premium Rice 2kg',      'Large family rice pack.', 11.80, 18, 2000, 'g',   4.5, 'active'),
  (3, 3, 9, 'Fresh Milk 1L',         'Full cream milk.',        6.90, 24, 1000, 'ml',  4.6, 'active'),
  (4, 6, 11, 'Frozen Potato Cubes',  'Ready-to-cook potato cubes.', 8.40, 20, 500, 'g', 4.1, 'active'),
  (4, 1, 12, 'Carrots 1kg',          'Crunchy carrots.',         5.10, 30, 1000, 'g',  4.3, 'active'),
  (3, 5, 13, 'Ground Coffee 250g',    'Medium roast coffee.',    14.50, 12, 250,  'g',   4.8, 'active'),
  (2, 3, 9, 'Low Fat Milk 1L',        'Low fat dairy milk.',      6.50, 28, 1000, 'ml',  4.2, 'active'),
  (1, 3, 10, 'Unsalted Butter 250g',  'Creamy baking butter.',    9.90, 18, 250,  'g',   4.5, 'active'),
  (3, 1, 4, 'Cherry Tomatoes 250g',   'Sweet salad tomatoes.',    5.80, 22, 250,  'g',   4.4, 'active'),
  (4, 1, 5, 'Red Onions 500g',        'Mild red onions.',         3.60, 26, 500,  'g',   4.1, 'active'),
  (2, 5, 6, 'Garlic Bulbs 200g',      'Fresh aromatic garlic.',   4.50, 24, 200,  'g',   4.3, 'active'),
  (3, 5, 7, 'Sea Salt 500g',          'Fine cooking sea salt.',   2.80, 40, 500,  'g',   4.0, 'active'),
  (2, 5, 8, 'Cooking Olive Oil 1L',   'Everyday olive oil.',     29.90, 12, 1000, 'ml',  4.5, 'active'),
  (4, 2, 2, 'Chicken Breast 250g',    'Small pack chicken breast.', 6.80, 34, 250, 'g',   4.2, 'active'),
  (3, 3, 3, 'Omega Eggs (6)',         'Pack of 6 omega eggs.',    5.60, 42, 6,    'pcs', 4.6, 'active'),
  (1, 6, 11, 'Hash Brown Potatoes',   'Frozen hash brown pack.', 10.90, 16, 600,  'g',   4.4, 'active'),
  (2, 7, 13, 'Cold Brew Coffee 500ml','Ready-to-drink coffee.',   7.90, 20, 500,  'ml',  4.3, 'active');
INSERT INTO recipes (user_id, recipe_title, description, instructions, base_servings, cuisine_type, difficulty, prep_time, cook_time, status) VALUES
  (3, 'Chicken Fried Rice', 'Quick weeknight chicken fried rice.', '1. Cook rice. 2. Stir-fry chicken. 3. Combine with eggs and onion.', 2, 'Asian',         'easy', 10, 20, 'active'),
  (3, 'Tomato Egg Stir-fry', 'Simple homestyle dish.',             '1. Beat eggs. 2. Cook tomatoes. 3. Combine and season.',           2, 'Chinese',       'easy', 5,  10, 'active'),
  (7, 'Creamy Mashed Potatoes', 'Soft mashed potatoes with butter and milk.', '1. Boil potatoes. 2. Mash with butter and milk. 3. Season to taste.', 4, 'Western', 'easy', 15, 25, 'active'),
  (8, 'Homemade Carrot Soup', 'A comforting carrot soup.', '1. Sauté garlic and onion. 2. Add carrots and stock. 3. Blend until smooth.', 3, 'Western', 'medium', 15, 30, 'active'),
  (9, 'Morning Coffee Blend', 'Simple home coffee recipe.', '1. Measure coffee. 2. Brew with hot water. 3. Serve immediately.', 1, 'Beverage', 'easy', 5, 5, 'active');
INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, unit) VALUES
  (1, 1, 400, 'g'),    -- Rice
  (1, 2, 300, 'g'),    -- Chicken
  (1, 3, 2,   'pcs'),  -- Eggs
  (1, 5, 100, 'g'),    -- Onion
  (1, 8, 30,  'ml'),   -- Oil
  (2, 3, 3,   'pcs'),  -- Eggs
  (2, 4, 300, 'g'),    -- Tomato
  (2, 8, 15,  'ml'),   -- Oil
  (3, 11, 1200, 'g'),  -- Potato
  (3, 9, 250, 'ml'),   -- Milk
  (3, 10, 40,  'g'),   -- Butter
  (4, 12, 300, 'g'),   -- Carrot
  (4, 5, 80,  'g'),    -- Onion
  (4, 6, 10,  'g'),    -- Garlic
  (5, 13, 20,  'g');   -- Coffee
