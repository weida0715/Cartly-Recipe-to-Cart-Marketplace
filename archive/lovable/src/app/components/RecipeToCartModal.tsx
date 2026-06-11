import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router';
import * as Dialog from '@radix-ui/react-dialog';
import { Button } from './ui/Button';
import { Card, CardContent, CardHeader, CardTitle } from './ui/Card';
import { Badge } from './ui/Badge';
import { X, ShoppingCart, Package, Check, Replace } from 'lucide-react';
import { Recipe, Ingredient, products, merchants } from '../data/mockData';
import { formatCurrency } from '../lib/utils';
import { toast } from 'sonner';

interface RecipeToCartModalProps {
  isOpen: boolean;
  onClose: () => void;
  recipe: Recipe;
  servings: number;
  scaledIngredients: Ingredient[];
}

interface MatchedProduct {
  ingredient: Ingredient;
  product: typeof products[0];
  quantity: number;
  merchantId: string;
}

export function RecipeToCartModal({
  isOpen,
  onClose,
  recipe,
  servings,
  scaledIngredients,
}: RecipeToCartModalProps) {
  const navigate = useNavigate();
  const [matchedProducts, setMatchedProducts] = useState<MatchedProduct[]>([]);
  const [isGenerating, setIsGenerating] = useState(false);

  useEffect(() => {
    if (isOpen) {
      generateCart();
    }
  }, [isOpen, scaledIngredients]);

  const generateCart = () => {
    setIsGenerating(true);
    setTimeout(() => {
      const matched = scaledIngredients
        .map((ingredient) => {
          const product = products.find((p) => p.id === ingredient.productId);
          if (product) {
            return {
              ingredient,
              product,
              quantity: 1,
              merchantId: product.merchantId,
            };
          }
          return null;
        })
        .filter((item): item is MatchedProduct => item !== null);

      setMatchedProducts(matched);
      setIsGenerating(false);
    }, 800);
  };

  const groupedByMerchant = matchedProducts.reduce((acc, item) => {
    if (!acc[item.merchantId]) {
      acc[item.merchantId] = [];
    }
    acc[item.merchantId].push(item);
    return acc;
  }, {} as Record<string, MatchedProduct[]>);

  const subtotal = matchedProducts.reduce(
    (sum, item) => sum + item.product.price * item.quantity,
    0
  );

  const deliveryEstimate = Object.keys(groupedByMerchant).length * 2.99;
  const total = subtotal + deliveryEstimate;

  const handleConfirm = () => {
    toast.success('Items added to cart!', {
      description: `${matchedProducts.length} items from ${Object.keys(groupedByMerchant).length} merchants`,
    });
    onClose();
    navigate('/cart');
  };

  return (
    <Dialog.Root open={isOpen} onOpenChange={onClose}>
      <Dialog.Portal>
        <Dialog.Overlay className="fixed inset-0 bg-black/50 z-50" />
        <Dialog.Content className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-background rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden z-50">
          <div className="flex flex-col max-h-[90vh]">
            {/* Header */}
            <div className="flex items-center justify-between p-6 border-b border-border">
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 bg-[var(--green-primary)] rounded-full flex items-center justify-center text-white">
                  <ShoppingCart className="h-6 w-6" />
                </div>
                <div>
                  <Dialog.Title className="text-xl">
                    Recipe-to-Cart Preview
                  </Dialog.Title>
                  <Dialog.Description className="text-sm text-muted-foreground">
                    Review ingredients grouped by merchant
                  </Dialog.Description>
                </div>
              </div>
              <Dialog.Close asChild>
                <Button variant="ghost" size="sm">
                  <X className="h-5 w-5" />
                </Button>
              </Dialog.Close>
            </div>

            {/* Content */}
            <div className="flex-1 overflow-y-auto p-6">
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Left Panel - Recipe Summary */}
                <div>
                  <Card className="bg-[var(--gray-bg)]">
                    <CardHeader>
                      <CardTitle className="flex items-center gap-2">
                        <span className="text-3xl">{recipe.image}</span>
                        <span>{recipe.title}</span>
                      </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                      <div className="flex items-center gap-2">
                        <Badge variant="secondary">
                          {servings} servings (adjusted from {recipe.servings})
                        </Badge>
                      </div>
                      <div>
                        <h4 className="mb-2">Scaled Ingredients:</h4>
                        <ul className="space-y-2">
                          {scaledIngredients.map((ing, idx) => (
                            <li
                              key={idx}
                              className="flex justify-between text-sm pb-2 border-b border-border last:border-0"
                            >
                              <span>{ing.name}</span>
                              <span className="text-muted-foreground">
                                {ing.amount.toFixed(ing.amount < 1 ? 2 : 1)} {ing.unit}
                              </span>
                            </li>
                          ))}
                        </ul>
                      </div>
                    </CardContent>
                  </Card>
                </div>

                {/* Right Panel - Generated Cart */}
                <div>
                  <h3 className="mb-4 flex items-center gap-2">
                    <Package className="h-5 w-5 text-[var(--green-primary)]" />
                    Generated Shopping Cart
                  </h3>
                  {isGenerating ? (
                    <div className="flex items-center justify-center h-48">
                      <div className="animate-spin rounded-full h-8 w-8 border-4 border-[var(--green-primary)] border-t-transparent"></div>
                    </div>
                  ) : (
                    <div className="space-y-4">
                      {Object.entries(groupedByMerchant).map(([merchantId, items]) => {
                        const merchant = merchants.find((m) => m.id === merchantId);
                        return (
                          <Card key={merchantId}>
                            <CardHeader className="pb-3">
                              <CardTitle className="text-base flex items-center gap-2">
                                <span className="text-xl">{merchant?.logo}</span>
                                {merchant?.name}
                              </CardTitle>
                            </CardHeader>
                            <CardContent className="pt-0">
                              <ul className="space-y-3">
                                {items.map((item, idx) => (
                                  <li
                                    key={idx}
                                    className="flex items-start gap-3 pb-3 border-b border-border last:border-0"
                                  >
                                    <div className="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-secondary to-accent/20 rounded flex items-center justify-center text-xl">
                                      {item.product.image}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                      <div className="text-sm mb-1">{item.product.name}</div>
                                      <div className="text-xs text-muted-foreground">
                                        {item.product.unit} • Qty: {item.quantity}
                                      </div>
                                    </div>
                                    <div className="flex-shrink-0 text-sm text-[var(--green-primary)]">
                                      {formatCurrency(item.product.price)}
                                    </div>
                                  </li>
                                ))}
                              </ul>
                            </CardContent>
                          </Card>
                        );
                      })}
                    </div>
                  )}
                </div>
              </div>
            </div>

            {/* Footer - Summary */}
            <div className="border-t border-border p-6 bg-[var(--gray-bg)]">
              <div className="space-y-2 mb-4">
                <div className="flex justify-between text-sm">
                  <span className="text-muted-foreground">Subtotal ({matchedProducts.length} items)</span>
                  <span>{formatCurrency(subtotal)}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-muted-foreground">
                    Delivery ({Object.keys(groupedByMerchant).length} merchants)
                  </span>
                  <span>{formatCurrency(deliveryEstimate)}</span>
                </div>
                <div className="flex justify-between border-t border-border pt-2">
                  <span>Total</span>
                  <span className="text-[var(--green-primary)]">{formatCurrency(total)}</span>
                </div>
              </div>
              <div className="flex gap-3">
                <Button variant="outline" className="flex-1" onClick={onClose}>
                  Cancel
                </Button>
                <Button
                  variant="primary"
                  className="flex-1"
                  onClick={handleConfirm}
                  disabled={isGenerating || matchedProducts.length === 0}
                >
                  <Check className="h-5 w-5" />
                  Add to Cart
                </Button>
              </div>
            </div>
          </div>
        </Dialog.Content>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
