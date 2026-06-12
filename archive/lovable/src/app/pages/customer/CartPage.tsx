import { useState } from 'react';
import { Link, useNavigate } from 'react-router';
import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Minus, Plus, X, ShoppingBag, Tag } from 'lucide-react';
import { products, merchants } from '../../data/mockData';
import { formatCurrency } from '../../lib/utils';

interface CartItem {
  productId: string;
  quantity: number;
}

export function CartPage() {
  const navigate = useNavigate();
  const [cartItems, setCartItems] = useState<CartItem[]>([
    { productId: 'p1', quantity: 1 },
    { productId: 'p2', quantity: 2 },
    { productId: 'p4', quantity: 1 },
  ]);
  const [voucherCode, setVoucherCode] = useState('');

  const cartProducts = cartItems
    .map((item) => ({
      ...item,
      product: products.find((p) => p.id === item.productId)!,
    }))
    .filter((item) => item.product);

  const groupedByMerchant = cartProducts.reduce((acc, item) => {
    const merchantId = item.product.merchantId;
    if (!acc[merchantId]) {
      acc[merchantId] = [];
    }
    acc[merchantId].push(item);
    return acc;
  }, {} as Record<string, typeof cartProducts>);

  const updateQuantity = (productId: string, newQuantity: number) => {
    if (newQuantity <= 0) {
      setCartItems(cartItems.filter((item) => item.productId !== productId));
    } else {
      setCartItems(
        cartItems.map((item) =>
          item.productId === productId ? { ...item, quantity: newQuantity } : item
        )
      );
    }
  };

  const removeItem = (productId: string) => {
    setCartItems(cartItems.filter((item) => item.productId !== productId));
  };

  const subtotal = cartProducts.reduce(
    (sum, item) => sum + item.product.price * item.quantity,
    0
  );
  const deliveryFee = Object.keys(groupedByMerchant).length * 2.99;
  const discount = 0;
  const total = subtotal + deliveryFee - discount;

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 className="mb-8">Shopping Cart</h1>

      {cartItems.length === 0 ? (
        <Card>
          <CardContent className="p-12 text-center">
            <ShoppingBag className="h-16 w-16 mx-auto mb-4 text-muted-foreground" />
            <h3 className="mb-2">Your cart is empty</h3>
            <p className="text-muted-foreground mb-6">
              Start adding delicious ingredients to your cart!
            </p>
            <div className="flex gap-4 justify-center">
              <Link to="/marketplace">
                <Button variant="primary">Browse Marketplace</Button>
              </Link>
              <Link to="/recipes">
                <Button variant="outline">Discover Recipes</Button>
              </Link>
            </div>
          </CardContent>
        </Card>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Cart Items */}
          <div className="lg:col-span-2 space-y-4">
            {Object.entries(groupedByMerchant).map(([merchantId, items]) => {
              const merchant = merchants.find((m) => m.id === merchantId);
              return (
                <Card key={merchantId}>
                  <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                      <span className="text-2xl">{merchant?.logo}</span>
                      {merchant?.name}
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-4">
                      {items.map((item) => (
                        <div
                          key={item.productId}
                          className="flex items-center gap-4 pb-4 border-b border-border last:border-0"
                        >
                          <div className="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-secondary to-accent/20 rounded-lg flex items-center justify-center text-3xl">
                            {item.product.image}
                          </div>
                          <div className="flex-1 min-w-0">
                            <h4 className="mb-1">{item.product.name}</h4>
                            <p className="text-sm text-muted-foreground mb-2">
                              {item.product.unit}
                            </p>
                            <div className="flex items-center gap-2">
                              <Button
                                variant="outline"
                                size="sm"
                                onClick={() =>
                                  updateQuantity(item.productId, item.quantity - 1)
                                }
                              >
                                <Minus className="h-3 w-3" />
                              </Button>
                              <span className="w-8 text-center">{item.quantity}</span>
                              <Button
                                variant="outline"
                                size="sm"
                                onClick={() =>
                                  updateQuantity(item.productId, item.quantity + 1)
                                }
                              >
                                <Plus className="h-3 w-3" />
                              </Button>
                            </div>
                          </div>
                          <div className="flex-shrink-0 text-right">
                            <div className="mb-2 text-[var(--green-primary)]">
                              {formatCurrency(item.product.price * item.quantity)}
                            </div>
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => removeItem(item.productId)}
                              className="text-destructive hover:text-destructive"
                            >
                              <X className="h-4 w-4" />
                            </Button>
                          </div>
                        </div>
                      ))}
                    </div>
                  </CardContent>
                </Card>
              );
            })}
          </div>

          {/* Order Summary */}
          <div>
            <Card className="sticky top-20">
              <CardHeader>
                <CardTitle>Order Summary</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">
                      Subtotal ({cartProducts.length} items)
                    </span>
                    <span>{formatCurrency(subtotal)}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">
                      Delivery ({Object.keys(groupedByMerchant).length} merchants)
                    </span>
                    <span>{formatCurrency(deliveryFee)}</span>
                  </div>
                  {discount > 0 && (
                    <div className="flex justify-between text-sm text-[var(--green-primary)]">
                      <span>Discount</span>
                      <span>-{formatCurrency(discount)}</span>
                    </div>
                  )}
                  <div className="border-t border-border pt-2 flex justify-between">
                    <span>Total</span>
                    <span className="text-[var(--green-primary)]">
                      {formatCurrency(total)}
                    </span>
                  </div>
                </div>

                <div>
                  <label className="text-sm mb-2 block">Voucher Code</label>
                  <div className="flex gap-2">
                    <Input
                      type="text"
                      placeholder="Enter code"
                      value={voucherCode}
                      onChange={(e) => setVoucherCode(e.target.value)}
                    />
                    <Button variant="outline">
                      <Tag className="h-4 w-4" />
                    </Button>
                  </div>
                </div>

                <Button
                  variant="primary"
                  className="w-full"
                  size="lg"
                  onClick={() => navigate('/checkout')}
                >
                  Proceed to Checkout
                </Button>

                <Link to="/recipes">
                  <Button variant="ghost" className="w-full">
                    Continue Shopping
                  </Button>
                </Link>
              </CardContent>
            </Card>
          </div>
        </div>
      )}
    </div>
  );
}
