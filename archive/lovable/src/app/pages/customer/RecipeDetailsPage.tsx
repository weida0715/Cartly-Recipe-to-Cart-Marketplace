import { useState } from 'react';
import { useParams, Link } from 'react-router';
import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Badge } from '../../components/ui/Badge';
import {
  Clock,
  Users,
  Star,
  ChefHat,
  ShoppingCart,
  Minus,
  Plus,
  Heart,
  Share2,
} from 'lucide-react';
import { recipes, products } from '../../data/mockData';
import { RecipeToCartModal } from '../../components/RecipeToCartModal';

export function RecipeDetailsPage() {
  const { id } = useParams();
  const recipe = recipes.find((r) => r.id === id);
  const [servings, setServings] = useState(recipe?.servings || 4);
  const [showCartModal, setShowCartModal] = useState(false);
  const [isSaved, setIsSaved] = useState(false);

  if (!recipe) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-16 text-center">
        <h2>Recipe not found</h2>
        <Link to="/recipes">
          <Button variant="primary" className="mt-4">
            Browse Recipes
          </Button>
        </Link>
      </div>
    );
  }

  const servingRatio = servings / recipe.servings;
  const scaledIngredients = recipe.ingredients.map((ing) => ({
    ...ing,
    amount: ing.amount * servingRatio,
  }));

  const handleGenerateCart = () => {
    setShowCartModal(true);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      {/* Recipe Header */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {/* Recipe Image */}
        <div className="aspect-video lg:aspect-square bg-gradient-to-br from-[var(--green-light)] to-[var(--green-primary)] rounded-xl flex items-center justify-center text-9xl">
          {recipe.image}
        </div>

        {/* Recipe Info */}
        <div>
          <div className="flex items-center gap-2 mb-4">
            <Badge variant="secondary">{recipe.cuisine}</Badge>
            <Badge variant={recipe.difficulty === 'Easy' ? 'success' : recipe.difficulty === 'Medium' ? 'warning' : 'danger'}>
              {recipe.difficulty}
            </Badge>
          </div>
          <h1 className="mb-4">{recipe.title}</h1>
          <p className="text-muted-foreground mb-6">{recipe.description}</p>

          <div className="grid grid-cols-3 gap-4 mb-6">
            <div className="flex items-center gap-2">
              <Clock className="h-5 w-5 text-[var(--green-primary)]" />
              <div>
                <div className="text-sm text-muted-foreground">Cook Time</div>
                <div>{recipe.cookTime} min</div>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Users className="h-5 w-5 text-[var(--green-primary)]" />
              <div>
                <div className="text-sm text-muted-foreground">Servings</div>
                <div>{recipe.servings}</div>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Star className="h-5 w-5 text-[var(--yellow-accent)] fill-[var(--yellow-accent)]" />
              <div>
                <div className="text-sm text-muted-foreground">Rating</div>
                <div>{recipe.rating} ({recipe.reviews})</div>
              </div>
            </div>
          </div>

          {/* Servings Selector */}
          <Card className="mb-6 bg-[var(--gray-bg)]">
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <div>
                  <div className="mb-1">Adjust Servings</div>
                  <div className="text-sm text-muted-foreground">
                    Ingredients will auto-calculate
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setServings(Math.max(1, servings - 1))}
                  >
                    <Minus className="h-4 w-4" />
                  </Button>
                  <span className="text-xl min-w-[3ch] text-center">{servings}</span>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setServings(servings + 1)}
                  >
                    <Plus className="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Action Buttons */}
          <div className="flex gap-3">
            <Button
              variant="primary"
              size="lg"
              className="flex-1"
              onClick={handleGenerateCart}
            >
              <ShoppingCart className="h-5 w-5" />
              Generate Cart
            </Button>
            <Button
              variant={isSaved ? 'accent' : 'outline'}
              size="lg"
              onClick={() => setIsSaved(!isSaved)}
            >
              <Heart className={isSaved ? 'fill-current' : ''} />
            </Button>
            <Button variant="outline" size="lg">
              <Share2 />
            </Button>
          </div>
        </div>
      </div>

      {/* Ingredients & Instructions */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Ingredients */}
        <div className="lg:col-span-1">
          <Card>
            <CardHeader>
              <CardTitle>Ingredients</CardTitle>
            </CardHeader>
            <CardContent>
              <ul className="space-y-3">
                {scaledIngredients.map((ingredient, index) => (
                  <li key={index} className="flex justify-between items-start gap-4 pb-3 border-b border-border last:border-0">
                    <span className="flex-1">{ingredient.name}</span>
                    <span className="text-muted-foreground whitespace-nowrap">
                      {ingredient.amount.toFixed(ingredient.amount < 1 ? 2 : 1)}{' '}
                      {ingredient.unit}
                    </span>
                  </li>
                ))}
              </ul>
            </CardContent>
          </Card>
        </div>

        {/* Instructions */}
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle>Instructions</CardTitle>
            </CardHeader>
            <CardContent>
              <ol className="space-y-4">
                {recipe.instructions.map((step, index) => (
                  <li key={index} className="flex gap-4">
                    <div className="flex-shrink-0 w-8 h-8 bg-[var(--green-primary)] text-white rounded-full flex items-center justify-center">
                      {index + 1}
                    </div>
                    <p className="flex-1 pt-1">{step}</p>
                  </li>
                ))}
              </ol>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Reviews Section */}
      <Card className="mt-8">
        <CardHeader>
          <CardTitle>Reviews ({recipe.reviews})</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="border-b border-border pb-4">
              <div className="flex items-center gap-2 mb-2">
                <div className="flex">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <Star
                      key={star}
                      className="h-4 w-4 fill-[var(--yellow-accent)] text-[var(--yellow-accent)]"
                    />
                  ))}
                </div>
                <span>Sarah Johnson</span>
                <span className="text-sm text-muted-foreground">• 2 days ago</span>
              </div>
              <p className="text-muted-foreground">
                Amazing recipe! The Recipe-to-Cart feature made shopping so easy. Got everything from 3 different merchants in one click!
              </p>
            </div>
            <div className="border-b border-border pb-4">
              <div className="flex items-center gap-2 mb-2">
                <div className="flex">
                  {[1, 2, 3, 4].map((star) => (
                    <Star
                      key={star}
                      className="h-4 w-4 fill-[var(--yellow-accent)] text-[var(--yellow-accent)]"
                    />
                  ))}
                  <Star className="h-4 w-4 text-[var(--yellow-accent)]" />
                </div>
                <span>Mike Chen</span>
                <span className="text-sm text-muted-foreground">• 1 week ago</span>
              </div>
              <p className="text-muted-foreground">
                Really good! Family loved it. Adjusted to 6 servings and the ingredient calculations were perfect.
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Recipe-to-Cart Modal */}
      <RecipeToCartModal
        isOpen={showCartModal}
        onClose={() => setShowCartModal(false)}
        recipe={recipe}
        servings={servings}
        scaledIngredients={scaledIngredients}
      />
    </div>
  );
}
