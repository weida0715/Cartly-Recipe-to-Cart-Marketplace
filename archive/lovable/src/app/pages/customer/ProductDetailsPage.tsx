import { useState } from 'react';
import { useParams, Link } from 'react-router';
import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Badge } from '../../components/ui/Badge';
import { ShoppingCart, Star, Minus, Plus, Store } from 'lucide-react';
import { products } from '../../data/mockData';
import { formatCurrency } from '../../lib/utils';
import { toast } from 'sonner';

export function ProductDetailsPage() {
  const { id } = useParams();
  const product = products.find((p) => p.id === id);
  const [quantity, setQuantity] = useState(1);

  if (!product) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-16 text-center">
        <h2>Product not found</h2>
        <Link to="/marketplace">
          <Button variant="primary" className="mt-4">
            Browse Marketplace
          </Button>
        </Link>
      </div>
    );
  }

  const handleAddToCart = () => {
    toast.success('Added to cart!', {
      description: `${quantity}x ${product.name}`,
    });
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {/* Product Image */}
        <div className="aspect-square bg-gradient-to-br from-secondary to-accent/20 rounded-xl flex items-center justify-center text-9xl">
          {product.image}
        </div>

        {/* Product Info */}
        <div>
          <Badge variant="secondary" className="mb-4">
            {product.category}
          </Badge>
          <h1 className="mb-4">{product.name}</h1>
          <div className="flex items-center gap-4 mb-6">
            <div className="flex items-center gap-1">
              {[1, 2, 3, 4, 5].map((star) => (
                <Star
                  key={star}
                  className={`h-5 w-5 ${
                    star <= Math.floor(product.rating)
                      ? 'fill-[var(--yellow-accent)] text-[var(--yellow-accent)]'
                      : 'text-muted-foreground'
                  }`}
                />
              ))}
            </div>
            <span className="text-muted-foreground">
              {product.rating} ({product.reviews} reviews)
            </span>
          </div>

          <div className="text-3xl text-[var(--green-primary)] mb-6">
            {formatCurrency(product.price)}
            <span className="text-base text-muted-foreground ml-2">/ {product.unit}</span>
          </div>

          <p className="text-muted-foreground mb-6">{product.description}</p>

          {/* Merchant Info */}
          <Card className="mb-6 bg-[var(--gray-bg)]">
            <CardContent className="p-4 flex items-center gap-3">
              <Store className="h-5 w-5 text-[var(--green-primary)]" />
              <div>
                <div className="text-sm text-muted-foreground">Sold by</div>
                <div>{product.merchantName}</div>
              </div>
            </CardContent>
          </Card>

          {/* Stock Status */}
          <div className="mb-6">
            <Badge variant={product.stock > 20 ? 'success' : product.stock > 0 ? 'warning' : 'danger'}>
              {product.stock > 0 ? `${product.stock} in stock` : 'Out of stock'}
            </Badge>
          </div>

          {/* Quantity Selector */}
          <div className="mb-6">
            <label className="text-sm mb-2 block">Quantity</label>
            <div className="flex items-center gap-3">
              <Button
                variant="outline"
                size="md"
                onClick={() => setQuantity(Math.max(1, quantity - 1))}
              >
                <Minus className="h-4 w-4" />
              </Button>
              <span className="text-xl min-w-[3ch] text-center">{quantity}</span>
              <Button
                variant="outline"
                size="md"
                onClick={() => setQuantity(Math.min(product.stock, quantity + 1))}
              >
                <Plus className="h-4 w-4" />
              </Button>
            </div>
          </div>

          {/* Add to Cart Button */}
          <div className="flex gap-3">
            <Button
              variant="primary"
              size="lg"
              className="flex-1"
              onClick={handleAddToCart}
              disabled={product.stock === 0}
            >
              <ShoppingCart className="h-5 w-5" />
              Add to Cart
            </Button>
          </div>
        </div>
      </div>

      {/* Reviews Section */}
      <Card className="mt-12">
        <CardContent className="p-6">
          <h3 className="mb-6">Customer Reviews</h3>
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
                <span>Emma Wilson</span>
                <span className="text-sm text-muted-foreground">• 1 week ago</span>
              </div>
              <p className="text-muted-foreground">
                Excellent quality! Fresh and exactly as described. Will order again.
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
                <span>David Lee</span>
                <span className="text-sm text-muted-foreground">• 2 weeks ago</span>
              </div>
              <p className="text-muted-foreground">
                Good product, delivered on time. Price is reasonable.
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
