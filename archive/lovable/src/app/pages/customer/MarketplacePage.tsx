import { useState } from 'react';
import { Link } from 'react-router';
import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Badge } from '../../components/ui/Badge';
import { Input } from '../../components/ui/Input';
import { Star, ShoppingCart, Filter, Search } from 'lucide-react';
import { products, categories } from '../../data/mockData';
import { formatCurrency } from '../../lib/utils';
import { toast } from 'sonner';

export function MarketplacePage() {
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [sortBy, setSortBy] = useState<'name' | 'price' | 'rating'>('name');

  const filteredProducts = products
    .filter((p) => {
      if (selectedCategory !== 'all' && p.category !== selectedCategory) return false;
      if (searchQuery && !p.name.toLowerCase().includes(searchQuery.toLowerCase())) return false;
      return true;
    })
    .sort((a, b) => {
      if (sortBy === 'price') return a.price - b.price;
      if (sortBy === 'rating') return b.rating - a.rating;
      return a.name.localeCompare(b.name);
    });

  const handleAddToCart = (productName: string) => {
    toast.success('Added to cart!', {
      description: productName,
    });
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 className="mb-8">Marketplace</h1>

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
                    placeholder="Search products..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="pl-10"
                  />
                </div>
              </div>

              {/* Categories */}
              <div className="mb-6">
                <label className="text-sm mb-2 block">Category</label>
                <div className="space-y-2">
                  <button
                    onClick={() => setSelectedCategory('all')}
                    className={`w-full text-left px-3 py-2 rounded-lg transition-colors ${
                      selectedCategory === 'all'
                        ? 'bg-[var(--green-primary)] text-white'
                        : 'hover:bg-secondary'
                    }`}
                  >
                    All Products
                  </button>
                  {categories.map((cat) => (
                    <button
                      key={cat.id}
                      onClick={() => setSelectedCategory(cat.name)}
                      className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-2 ${
                        selectedCategory === cat.name
                          ? 'bg-[var(--green-primary)] text-white'
                          : 'hover:bg-secondary'
                      }`}
                    >
                      <span>{cat.icon}</span>
                      <span>{cat.name}</span>
                    </button>
                  ))}
                </div>
              </div>

              {/* Sort */}
              <div>
                <label className="text-sm mb-2 block">Sort By</label>
                <select
                  value={sortBy}
                  onChange={(e) => setSortBy(e.target.value as any)}
                  className="w-full h-10 px-3 rounded-lg border border-border bg-input-background"
                >
                  <option value="name">Name</option>
                  <option value="price">Price</option>
                  <option value="rating">Rating</option>
                </select>
              </div>
            </CardContent>
          </Card>
        </aside>

        {/* Products Grid */}
        <div className="lg:col-span-3">
          <div className="mb-4 text-sm text-muted-foreground">
            Showing {filteredProducts.length} products
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredProducts.map((product) => (
              <Card key={product.id} className="hover:shadow-lg transition-shadow">
                <CardContent className="p-4">
                  <Link to={`/product/${product.id}`}>
                    <div className="aspect-square bg-gradient-to-br from-secondary to-accent/20 rounded-lg flex items-center justify-center text-6xl mb-4 cursor-pointer hover:scale-105 transition-transform">
                      {product.image}
                    </div>
                  </Link>
                  <div className="flex items-center gap-2 mb-2">
                    <Badge variant="secondary" className="text-xs">
                      {product.category}
                    </Badge>
                    <div className="flex items-center gap-1 text-xs">
                      <Star className="h-3 w-3 fill-[var(--yellow-accent)] text-[var(--yellow-accent)]" />
                      {product.rating}
                    </div>
                  </div>
                  <Link to={`/product/${product.id}`}>
                    <h4 className="mb-1 hover:text-[var(--green-primary)] transition-colors cursor-pointer">
                      {product.name}
                    </h4>
                  </Link>
                  <p className="text-sm text-muted-foreground mb-3">{product.merchantName}</p>
                  <div className="flex items-center justify-between">
                    <div>
                      <div className="text-[var(--green-primary)]">{formatCurrency(product.price)}</div>
                      <div className="text-xs text-muted-foreground">{product.unit}</div>
                    </div>
                    <Button
                      variant="primary"
                      size="sm"
                      onClick={() => handleAddToCart(product.name)}
                    >
                      <ShoppingCart className="h-4 w-4" />
                    </Button>
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
