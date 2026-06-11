import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { Badge } from '../../components/ui/Badge';
import { Button } from '../../components/ui/Button';
import { Package, Eye } from 'lucide-react';
import { formatCurrency } from '../../lib/utils';

export function OrderHistoryPage() {
  const orders = [
    {
      id: 'ORD-001',
      date: '2026-05-05',
      status: 'delivered',
      total: 45.97,
      items: 5,
    },
    {
      id: 'ORD-002',
      date: '2026-04-28',
      status: 'shipped',
      total: 32.45,
      items: 3,
    },
    {
      id: 'ORD-003',
      date: '2026-04-20',
      status: 'delivered',
      total: 28.99,
      items: 4,
    },
    {
      id: 'ORD-004',
      date: '2026-04-15',
      status: 'delivered',
      total: 56.32,
      items: 7,
    },
  ];

  return (
    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 className="mb-8">Order History</h1>

      <div className="space-y-4">
        {orders.map((order) => (
          <Card key={order.id}>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center gap-2">
                  <Package className="h-5 w-5" />
                  {order.id}
                </CardTitle>
                <Badge
                  variant={
                    order.status === 'delivered'
                      ? 'success'
                      : order.status === 'shipped'
                      ? 'warning'
                      : order.status === 'preparing'
                      ? 'secondary'
                      : 'default'
                  }
                >
                  {order.status}
                </Badge>
              </div>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                  <div className="text-sm text-muted-foreground">Order Date</div>
                  <div>{order.date}</div>
                </div>
                <div>
                  <div className="text-sm text-muted-foreground">Items</div>
                  <div>{order.items} items</div>
                </div>
                <div>
                  <div className="text-sm text-muted-foreground">Total</div>
                  <div className="text-[var(--green-primary)]">
                    {formatCurrency(order.total)}
                  </div>
                </div>
                <div className="flex items-end">
                  <Button variant="outline" size="sm" className="w-full">
                    <Eye className="h-4 w-4" />
                    View Details
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
