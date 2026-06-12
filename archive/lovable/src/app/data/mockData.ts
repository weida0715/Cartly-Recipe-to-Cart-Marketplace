export interface Product {
  id: string;
  name: string;
  description: string;
  price: number;
  image: string;
  category: string;
  merchantId: string;
  merchantName: string;
  rating: number;
  reviews: number;
  stock: number;
  unit: string;
}

export interface Recipe {
  id: string;
  title: string;
  description: string;
  image: string;
  cookTime: number;
  difficulty: 'Easy' | 'Medium' | 'Hard';
  servings: number;
  rating: number;
  reviews: number;
  cuisine: string;
  ingredients: Ingredient[];
  instructions: string[];
}

export interface Ingredient {
  name: string;
  amount: number;
  unit: string;
  productId?: string;
}

export interface Merchant {
  id: string;
  name: string;
  logo: string;
  rating: number;
  description: string;
  status: 'active' | 'pending' | 'suspended';
}

export interface CartItem {
  productId: string;
  quantity: number;
  merchantId: string;
}

export interface Order {
  id: string;
  customerId: string;
  items: CartItem[];
  total: number;
  status: 'pending' | 'preparing' | 'shipped' | 'delivered' | 'cancelled';
  date: string;
  deliveryAddress: string;
}

export const merchants: Merchant[] = [
  {
    id: 'm1',
    name: 'Fresh Farms Market',
    logo: '🌾',
    rating: 4.8,
    description: 'Premium organic produce and dairy',
    status: 'active',
  },
  {
    id: 'm2',
    name: 'Asian Grocery Store',
    logo: '🏮',
    rating: 4.6,
    description: 'Authentic Asian ingredients and spices',
    status: 'active',
  },
  {
    id: 'm3',
    name: 'Butcher\'s Best',
    logo: '🥩',
    rating: 4.9,
    description: 'Quality meats and poultry',
    status: 'active',
  },
  {
    id: 'm4',
    name: 'Spice Haven',
    logo: '🌶️',
    rating: 4.7,
    description: 'Exotic spices and condiments',
    status: 'active',
  },
];

export const products: Product[] = [
  {
    id: 'p1',
    name: 'Jasmine Rice',
    description: 'Premium Thai jasmine rice, 5kg bag',
    price: 12.99,
    image: '🍚',
    category: 'Grains',
    merchantId: 'm2',
    merchantName: 'Asian Grocery Store',
    rating: 4.8,
    reviews: 245,
    stock: 50,
    unit: '5kg',
  },
  {
    id: 'p2',
    name: 'Free-Range Eggs',
    description: 'Organic free-range eggs, dozen',
    price: 5.99,
    image: '🥚',
    category: 'Dairy & Eggs',
    merchantId: 'm1',
    merchantName: 'Fresh Farms Market',
    rating: 4.9,
    reviews: 312,
    stock: 100,
    unit: '12 eggs',
  },
  {
    id: 'p3',
    name: 'Chicken Breast',
    description: 'Fresh boneless chicken breast, 500g',
    price: 8.99,
    image: '🍗',
    category: 'Meat',
    merchantId: 'm3',
    merchantName: 'Butcher\'s Best',
    rating: 4.7,
    reviews: 189,
    stock: 30,
    unit: '500g',
  },
  {
    id: 'p4',
    name: 'Soy Sauce',
    description: 'Premium dark soy sauce, 500ml',
    price: 4.49,
    image: '🥫',
    category: 'Condiments',
    merchantId: 'm2',
    merchantName: 'Asian Grocery Store',
    rating: 4.6,
    reviews: 156,
    stock: 80,
    unit: '500ml',
  },
  {
    id: 'p5',
    name: 'Olive Oil',
    description: 'Extra virgin olive oil, 750ml',
    price: 14.99,
    image: '🫒',
    category: 'Oils',
    merchantId: 'm1',
    merchantName: 'Fresh Farms Market',
    rating: 4.8,
    reviews: 201,
    stock: 45,
    unit: '750ml',
  },
  {
    id: 'p6',
    name: 'Garlic',
    description: 'Fresh garlic bulbs, 250g',
    price: 3.49,
    image: '🧄',
    category: 'Vegetables',
    merchantId: 'm1',
    merchantName: 'Fresh Farms Market',
    rating: 4.7,
    reviews: 98,
    stock: 120,
    unit: '250g',
  },
  {
    id: 'p7',
    name: 'Ginger',
    description: 'Fresh ginger root, 200g',
    price: 2.99,
    image: '🫚',
    category: 'Vegetables',
    merchantId: 'm2',
    merchantName: 'Asian Grocery Store',
    rating: 4.6,
    reviews: 87,
    stock: 90,
    unit: '200g',
  },
  {
    id: 'p8',
    name: 'Bell Peppers',
    description: 'Mixed bell peppers, 3 pack',
    price: 4.99,
    image: '🫑',
    category: 'Vegetables',
    merchantId: 'm1',
    merchantName: 'Fresh Farms Market',
    rating: 4.5,
    reviews: 134,
    stock: 60,
    unit: '3 pack',
  },
  {
    id: 'p9',
    name: 'Tomatoes',
    description: 'Fresh vine tomatoes, 500g',
    price: 3.99,
    image: '🍅',
    category: 'Vegetables',
    merchantId: 'm1',
    merchantName: 'Fresh Farms Market',
    rating: 4.7,
    reviews: 176,
    stock: 85,
    unit: '500g',
  },
  {
    id: 'p10',
    name: 'Chili Powder',
    description: 'Ground chili powder, 100g',
    price: 3.99,
    image: '🌶️',
    category: 'Spices',
    merchantId: 'm4',
    merchantName: 'Spice Haven',
    rating: 4.8,
    reviews: 143,
    stock: 70,
    unit: '100g',
  },
];

export const recipes: Recipe[] = [
  {
    id: 'r1',
    title: 'Classic Fried Rice',
    description: 'A delicious and easy Asian-style fried rice with vegetables and eggs',
    image: '🍛',
    cookTime: 25,
    difficulty: 'Easy',
    servings: 4,
    rating: 4.8,
    reviews: 342,
    cuisine: 'Asian',
    ingredients: [
      { name: 'Jasmine Rice', amount: 2, unit: 'cups', productId: 'p1' },
      { name: 'Eggs', amount: 3, unit: 'pieces', productId: 'p2' },
      { name: 'Soy Sauce', amount: 3, unit: 'tbsp', productId: 'p4' },
      { name: 'Olive Oil', amount: 2, unit: 'tbsp', productId: 'p5' },
      { name: 'Garlic', amount: 2, unit: 'cloves', productId: 'p6' },
      { name: 'Mixed Vegetables', amount: 1, unit: 'cup' },
    ],
    instructions: [
      'Cook rice according to package directions and let it cool.',
      'Heat oil in a large wok or pan over high heat.',
      'Scramble eggs and set aside.',
      'Stir-fry garlic until fragrant.',
      'Add vegetables and rice, stir-fry for 3-4 minutes.',
      'Add soy sauce and eggs, mix well.',
      'Serve hot and enjoy!',
    ],
  },
  {
    id: 'r2',
    title: 'Honey Garlic Chicken',
    description: 'Tender chicken breast glazed with a sweet and savory honey garlic sauce',
    image: '🍗',
    cookTime: 30,
    difficulty: 'Medium',
    servings: 4,
    rating: 4.9,
    reviews: 567,
    cuisine: 'American',
    ingredients: [
      { name: 'Chicken Breast', amount: 800, unit: 'g', productId: 'p3' },
      { name: 'Garlic', amount: 4, unit: 'cloves', productId: 'p6' },
      { name: 'Honey', amount: 4, unit: 'tbsp' },
      { name: 'Soy Sauce', amount: 3, unit: 'tbsp', productId: 'p4' },
      { name: 'Olive Oil', amount: 2, unit: 'tbsp', productId: 'p5' },
      { name: 'Ginger', amount: 1, unit: 'tsp', productId: 'p7' },
    ],
    instructions: [
      'Cut chicken into bite-sized pieces.',
      'Mix honey, soy sauce, minced garlic, and ginger in a bowl.',
      'Heat oil in a pan over medium-high heat.',
      'Cook chicken until golden brown, about 6-8 minutes.',
      'Pour sauce over chicken and simmer for 5 minutes.',
      'Serve with rice or vegetables.',
    ],
  },
  {
    id: 'r3',
    title: 'Spicy Stir-Fry Vegetables',
    description: 'Colorful vegetables in a spicy Asian-inspired sauce',
    image: '🥗',
    cookTime: 15,
    difficulty: 'Easy',
    servings: 3,
    rating: 4.6,
    reviews: 234,
    cuisine: 'Asian',
    ingredients: [
      { name: 'Bell Peppers', amount: 2, unit: 'pieces', productId: 'p8' },
      { name: 'Garlic', amount: 3, unit: 'cloves', productId: 'p6' },
      { name: 'Ginger', amount: 1, unit: 'tbsp', productId: 'p7' },
      { name: 'Soy Sauce', amount: 2, unit: 'tbsp', productId: 'p4' },
      { name: 'Chili Powder', amount: 1, unit: 'tsp', productId: 'p10' },
      { name: 'Olive Oil', amount: 2, unit: 'tbsp', productId: 'p5' },
    ],
    instructions: [
      'Cut bell peppers into strips.',
      'Heat oil in a wok over high heat.',
      'Add garlic and ginger, stir-fry for 30 seconds.',
      'Add peppers and other vegetables, stir-fry for 5 minutes.',
      'Add soy sauce and chili powder, toss to combine.',
      'Serve immediately.',
    ],
  },
  {
    id: 'r4',
    title: 'Tomato Egg Drop Soup',
    description: 'A comforting Chinese soup with silky eggs and fresh tomatoes',
    image: '🍲',
    cookTime: 20,
    difficulty: 'Easy',
    servings: 4,
    rating: 4.7,
    reviews: 189,
    cuisine: 'Chinese',
    ingredients: [
      { name: 'Tomatoes', amount: 400, unit: 'g', productId: 'p9' },
      { name: 'Eggs', amount: 3, unit: 'pieces', productId: 'p2' },
      { name: 'Garlic', amount: 2, unit: 'cloves', productId: 'p6' },
      { name: 'Ginger', amount: 1, unit: 'tsp', productId: 'p7' },
      { name: 'Chicken Stock', amount: 4, unit: 'cups' },
    ],
    instructions: [
      'Cut tomatoes into wedges.',
      'Sauté garlic and ginger until fragrant.',
      'Add tomatoes and cook until soft.',
      'Pour in chicken stock and bring to boil.',
      'Slowly pour beaten eggs while stirring.',
      'Season with salt and serve hot.',
    ],
  },
];

export const categories = [
  { id: 'c1', name: 'Vegetables', icon: '🥬' },
  { id: 'c2', name: 'Fruits', icon: '🍎' },
  { id: 'c3', name: 'Meat', icon: '🥩' },
  { id: 'c4', name: 'Dairy & Eggs', icon: '🥛' },
  { id: 'c5', name: 'Grains', icon: '🌾' },
  { id: 'c6', name: 'Spices', icon: '🌶️' },
  { id: 'c7', name: 'Condiments', icon: '🥫' },
  { id: 'c8', name: 'Oils', icon: '🫒' },
];
