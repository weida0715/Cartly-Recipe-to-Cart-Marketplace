import { useState } from 'react';
import { useNavigate } from 'react-router';
import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { MapPin, CreditCard, Package } from 'lucide-react';
import { formatCurrency } from '../../lib/utils';
import { toast } from 'sonner';

export function CheckoutPage() {
  const navigate = useNavigate();
  const [step, setStep] = useState<'address' | 'payment' | 'review'>('address');

  const handlePlaceOrder = () => {
    toast.success('Order placed successfully!');
    navigate('/order-confirmation/ORD-123');
  };

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 className="mb-8">Checkout</h1>

      {/* Progress Indicator */}
      <div className="flex items-center justify-center gap-4 mb-12">
        {[
          { key: 'address', label: 'Address', icon: MapPin },
          { key: 'payment', label: 'Payment', icon: CreditCard },
          { key: 'review', label: 'Review', icon: Package },
        ].map((s, idx) => {
          const Icon = s.icon;
          const isActive = s.key === step;
          const isPast =
            (s.key === 'address' && step !== 'address') ||
            (s.key === 'payment' && step === 'review');
          return (
            <div key={s.key} className="flex items-center gap-4">
              <div className="flex flex-col items-center">
                <div
                  className={`w-12 h-12 rounded-full flex items-center justify-center ${
                    isActive
                      ? 'bg-[var(--green-primary)] text-white'
                      : isPast
                      ? 'bg-[var(--green-light)] text-white'
                      : 'bg-secondary text-muted-foreground'
                  }`}
                >
                  <Icon className="h-6 w-6" />
                </div>
                <span className="text-sm mt-2">{s.label}</span>
              </div>
              {idx < 2 && (
                <div
                  className={`w-16 h-1 ${
                    isPast ? 'bg-[var(--green-light)]' : 'bg-border'
                  }`}
                />
              )}
            </div>
          );
        })}
      </div>

      {/* Address Step */}
      {step === 'address' && (
        <Card>
          <CardHeader>
            <CardTitle>Delivery Address</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-sm mb-2 block">First Name</label>
                <Input type="text" placeholder="John" />
              </div>
              <div>
                <label className="text-sm mb-2 block">Last Name</label>
                <Input type="text" placeholder="Doe" />
              </div>
            </div>
            <div>
              <label className="text-sm mb-2 block">Address</label>
              <Input type="text" placeholder="123 Main Street" />
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-sm mb-2 block">City</label>
                <Input type="text" placeholder="San Francisco" />
              </div>
              <div>
                <label className="text-sm mb-2 block">ZIP Code</label>
                <Input type="text" placeholder="94102" />
              </div>
            </div>
            <div>
              <label className="text-sm mb-2 block">Phone</label>
              <Input type="tel" placeholder="+1 (555) 123-4567" />
            </div>
            <Button
              variant="primary"
              size="lg"
              className="w-full"
              onClick={() => setStep('payment')}
            >
              Continue to Payment
            </Button>
          </CardContent>
        </Card>
      )}

      {/* Payment Step */}
      {step === 'payment' && (
        <Card>
          <CardHeader>
            <CardTitle>Payment Method</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <label className="text-sm mb-2 block">Card Number</label>
              <Input type="text" placeholder="1234 5678 9012 3456" />
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-sm mb-2 block">Expiry Date</label>
                <Input type="text" placeholder="MM/YY" />
              </div>
              <div>
                <label className="text-sm mb-2 block">CVV</label>
                <Input type="text" placeholder="123" />
              </div>
            </div>
            <div>
              <label className="text-sm mb-2 block">Cardholder Name</label>
              <Input type="text" placeholder="John Doe" />
            </div>
            <div className="flex gap-3">
              <Button variant="outline" className="flex-1" onClick={() => setStep('address')}>
                Back
              </Button>
              <Button
                variant="primary"
                size="lg"
                className="flex-1"
                onClick={() => setStep('review')}
              >
                Review Order
              </Button>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Review Step */}
      {step === 'review' && (
        <div className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Order Summary</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Subtotal (3 items)</span>
                  <span>{formatCurrency(42.47)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Delivery</span>
                  <span>{formatCurrency(5.98)}</span>
                </div>
                <div className="border-t border-border pt-2 flex justify-between">
                  <span>Total</span>
                  <span className="text-[var(--green-primary)]">{formatCurrency(48.45)}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Delivery Address</CardTitle>
            </CardHeader>
            <CardContent>
              <p>John Doe</p>
              <p className="text-muted-foreground">123 Main Street</p>
              <p className="text-muted-foreground">San Francisco, CA 94102</p>
            </CardContent>
          </Card>

          <div className="flex gap-3">
            <Button variant="outline" className="flex-1" onClick={() => setStep('payment')}>
              Back
            </Button>
            <Button
              variant="primary"
              size="lg"
              className="flex-1"
              onClick={handlePlaceOrder}
            >
              Place Order
            </Button>
          </div>
        </div>
      )}
    </div>
  );
}
