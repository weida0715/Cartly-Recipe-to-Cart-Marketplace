import { useState } from 'react';
import { Link } from 'react-router';
import { Card, CardContent } from '../../components/ui/Card';
import { Badge } from '../../components/ui/Badge';
import { Input } from '../../components/ui/Input';
import { Star, Clock, Filter, Search, Heart } from 'lucide-react';
import { recipes } from '../../data/mockData';

export function RecipeDiscoveryPage() {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCuisine, setSelectedCuisine] = useState<string>('all');
  const [selectedDifficulty, setSelectedDifficulty] = useState<string>('all');

  const cuisines = ['all', 'Asian', 'American', 'Chinese', 'Italian', 'Mexican'];
  const difficulties = ['all', 'Easy', 'Medium', 'Hard'];

  const filteredRecipes = recipes.filter((recipe) => {
    if (searchQuery && !recipe.title.toLowerCase().includes(searchQuery.toLowerCase())) return false;
    if (selectedCuisine !== 'all' && recipe.cuisine !== selectedCuisine) return false;
    if (selectedDifficulty !== 'all' && recipe.difficulty !== selectedDifficulty) return false;
    return true;
  });

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 className="mb-8">Discover Recipes</h1>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {/* Filters Sidebar */}
        <aside className="lg:col-span-1">
          <Card>
            <CardContent className="p-4">
              <h3 className="mb-4 flex items-center gap-2">
                <Filter className="h-5 w-5" />
                Filters
              </h3>

              {/* Search */}
              <div className="mb-6">
                <label className="text-sm mb-2 block">Search</label>
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <Input
                    type="search"
                    placeholder="Search recipes..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="pl-10"
                  />
                </div>
              </div>

              {/* Cuisine */}
              <div className="mb-6">
                <label className="text-sm mb-2 block">Cuisine</label>
                <div className="space-y-2">
                  {cuisines.map((cuisine) => (
                    <button
                      key={cuisine}
                      onClick={() => setSelectedCuisine(cuisine)}
                      className={`w-full text-left px-3 py-2 rounded-lg transition-colors ${
                        selectedCuisine === cuisine
                          ? 'bg-[var(--green-primary)] text-white'
                          : 'hover:bg-secondary'
                      }`}
                    >
                      {cuisine === 'all' ? 'All Cuisines' : cuisine}
                    </button>
                  ))}
                </div>
              </div>

              {/* Difficulty */}
              <div>
                <label className="text-sm mb-2 block">Difficulty</label>
                <div className="space-y-2">
                  {difficulties.map((difficulty) => (
                    <button
                      key={difficulty}
                      onClick={() => setSelectedDifficulty(difficulty)}
                      className={`w-full text-left px-3 py-2 rounded-lg transition-colors ${
                        selectedDifficulty === difficulty
                          ? 'bg-[var(--green-primary)] text-white'
                          : 'hover:bg-secondary'
                      }`}
                    >
                      {difficulty === 'all' ? 'All Levels' : difficulty}
                    </button>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>
        </aside>

        {/* Recipes Grid */}
        <div className="lg:col-span-3">
          <div className="mb-4 text-sm text-muted-foreground">
            Showing {filteredRecipes.length} recipes
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {filteredRecipes.map((recipe) => (
              <Card key={recipe.id} className="hover:shadow-lg transition-shadow overflow-hidden">
                <Link to={`/recipe/${recipe.id}`}>
                  <div className="aspect-video bg-gradient-to-br from-[var(--green-light)] to-[var(--green-primary)] flex items-center justify-center text-7xl cursor-pointer hover:scale-105 transition-transform">
                    {recipe.image}
                  </div>
                </Link>
                <CardContent className="p-4">
                  <div className="flex items-center gap-2 mb-2">
                    <Badge variant="secondary">{recipe.cuisine}</Badge>
                    <Badge
                      variant={
                        recipe.difficulty === 'Easy'
                          ? 'success'
                          : recipe.difficulty === 'Medium'
                          ? 'warning'
                          : 'danger'
                      }
                    >
                      {recipe.difficulty}
                    </Badge>
                  </div>
                  <Link to={`/recipe/${recipe.id}`}>
                    <h3 className="mb-2 hover:text-[var(--green-primary)] transition-colors cursor-pointer">
                      {recipe.title}
                    </h3>
                  </Link>
                  <p className="text-sm text-muted-foreground mb-4 line-clamp-2">
                    {recipe.description}
                  </p>
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4 text-sm text-muted-foreground">
                      <div className="flex items-center gap-1">
                        <Clock className="h-4 w-4" />
                        {recipe.cookTime}m
                      </div>
                      <div className="flex items-center gap-1">
                        <Star className="h-4 w-4 fill-[var(--yellow-accent)] text-[var(--yellow-accent)]" />
                        {recipe.rating}
                      </div>
                    </div>
                    <button className="text-muted-foreground hover:text-[var(--orange-accent)] transition-colors">
                      <Heart className="h-5 w-5" />
                    </button>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
