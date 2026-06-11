import { Link } from 'react-router';
import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Star, Clock, ChefHat, ShoppingCart, ArrowRight } from 'lucide-react';
import { recipes, products, categories } from '../../data/mockData';
import { Badge } from '../../components/ui/Badge';
import { formatCurrency } from '../../lib/utils';

export function HomePage() {
  const featuredRecipes = recipes.slice(0, 3);
  const featuredProducts = products.slice(0, 4);

  return (
    <div>
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-[var(--green-primary)] to-[var(--green-dark)] text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
          <div className="text-center max-w-3xl mx-auto">
            <h1 className="text-4xl md:text-5xl mb-6">
              Cook Smarter with Recipe-to-Cart
            </h1>
            <p className="text-xl mb-8 text-white/90">
              Discover delicious recipes and instantly add all ingredients to your cart from multiple merchants
            </p>
            <div className="flex gap-4 justify-center">
              <Link to="/recipes">
                <Button variant="accent" size="lg">
                  <ChefHat className="h-5 w-5" />
                  Browse Recipes
                </Button>
              </Link>
              <Link to="/marketplace">
                <Button variant="secondary" size="lg">
                  Shop Groceries
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* How It Works */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-center mb-12">How Recipe-to-Cart Works</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="text-center">
              <div className="w-16 h-16 bg-[var(--green-light)] rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                1️⃣
              </div>
              <h3 className="mb-2">Choose a Recipe</h3>
              <p className="text-muted-foreground">
                Browse thousands of recipes and select your favorite
              </p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-[var(--green-light)] rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                2️⃣
              </div>
              <h3 className="mb-2">Adjust Servings</h3>
              <p className="text-muted-foreground">
                Set the number of servings and ingredients auto-calculate
              </p>
            </div>
            <div className="text-center">
              <div className="w-16 h-16 bg-[var(--green-light)] rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                3️⃣
              </div>
              <h3 className="mb-2">Generate Cart</h3>
              <p className="text-muted-foreground">
                All ingredients added to cart, grouped by merchant
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Recipes */}
      <section className="py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center mb-8">
            <h2>Featured Recipes</h2>
            <Link to="/recipes">
              <Button variant="ghost">
                View All
                <ArrowRight className="h-4 w-4" />
              </Button>
            </Link>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {featuredRecipes.map((recipe) => (
              <Link key={recipe.id} to={`/recipe/${recipe.id}`}>
                <Card className="hover:shadow-lg transition-shadow cursor-pointer overflow-hidden">
                  <div className="aspect-video bg-gradient-to-br from-[var(--green-light)] to-[var(--green-primary)] flex items-center justify-center text-6xl">
                    {recipe.image}
                  </div>
                  <CardContent className="p-4">
                    <h3 className="mb-2">{recipe.title}</h3>
                    <p className="text-sm text-muted-foreground mb-4 line-clamp-2">
                      {recipe.description}
                    </p>
                    <div className="flex items-center gap-4 text-sm text-muted-foreground">
                      <div className="flex items-center gap-1">
                        <Clock className="h-4 w-4" />
                        {recipe.cookTime}m
                      </div>
                      <div className="flex items-center gap-1">
                        <Star className="h-4 w-4 fill-[var(--yellow-accent)] text-[var(--yellow-accent)]" />
                        {recipe.rating}
                      </div>
                      <Badge variant="secondary">{recipe.difficulty}</Badge>
                    </div>
                  </CardContent>
                </Card>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Popular Categories */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-center mb-12">Shop by Category</h2>
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            {categories.map((category) => (
              <Link key={category.id} to="/marketplace">
                <Card className="hover:shadow-md transition-shadow cursor-pointer">
                  <CardContent className="p-6 text-center">
                    <div className="text-4xl mb-2">{category.icon}</div>
                    <div className="text-sm">{category.name}</div>
                  </CardContent>
                </Card>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center mb-8">
            <h2>Featured Products</h2>
            <Link to="/marketplace">
              <Button variant="ghost">
                View All
                <ArrowRight className="h-4 w-4" />
              </Button>
            </Link>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            {featuredProducts.map((product) => (
              <Link key={product.id} to={`/product/${product.id}`}>
                <Card className="hover:shadow-lg transition-shadow cursor-pointer">
                  <CardContent className="p-4">
                    <div className="aspect-square bg-gradient-to-br from-secondary to-accent/20 rounded-lg flex items-center justify-center text-6xl mb-4">
                      {product.image}
                    </div>
                    <h4 className="mb-1">{product.name}</h4>
                    <p className="text-sm text-muted-foreground mb-2">{product.merchantName}</p>
                    <div className="flex items-center justify-between">
                      <span className="text-[var(--green-primary)]">{formatCurrency(product.price)}</span>
                      <Button variant="primary" size="sm">
                        <ShoppingCart className="h-4 w-4" />
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Promotions */}
      <section className="py-16 bg-gradient-to-r from-[var(--orange-accent)] to-[var(--yellow-accent)] text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="mb-4">Special Offer: First Order 20% Off!</h2>
          <p className="text-xl mb-6">
            Use code <strong>CARTLY20</strong> at checkout
          </p>
          <Link to="/marketplace">
            <Button variant="secondary" size="lg">
              Start Shopping
            </Button>
          </Link>
        </div>
      </section>
    </div>
  );
}
