import { Link } from 'react-router';
import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { CheckCircle, Package, Home } from 'lucide-react';

export function OrderConfirmationPage() {
  return (
    <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <Card>
        <CardContent className="p-12 text-center">
          <div className="w-20 h-20 bg-[var(--green-primary)] rounded-full flex items-center justify-center mx-auto mb-6">
            <CheckCircle className="h-12 w-12 text-white" />
          </div>
          <h1 className="mb-4">Order Confirmed!</h1>
          <p className="text-muted-foreground mb-2">
            Your order has been successfully placed.
          </p>
          <p className="text-2xl text-[var(--green-primary)] mb-8">
            Order #ORD-123
          </p>
          <div className="bg-[var(--gray-bg)] rounded-lg p-6 mb-8">
            <p className="text-muted-foreground mb-2">Estimated Delivery</p>
            <p className="text-xl">May 12, 2026</p>
          </div>
          <div className="flex gap-3 justify-center">
            <Link to="/orders">
              <Button variant="primary" size="lg">
                <Package className="h-5 w-5" />
                Track Order
              </Button>
            </Link>
            <Link to="/">
              <Button variant="outline" size="lg">
                <Home className="h-5 w-5" />
                Back to Home
              </Button>
            </Link>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
