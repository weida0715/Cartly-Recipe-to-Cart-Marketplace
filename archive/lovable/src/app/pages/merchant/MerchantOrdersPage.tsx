import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Badge } from '../../components/ui/Badge';
import { Eye } from 'lucide-react';
import { formatCurrency } from '../../lib/utils';

export function MerchantOrdersPage() {
  const orders = [
    {
      id: 'ORD-156',
      customer: 'Sarah Johnson',
      date: '2026-05-08',
      items: 3,
      total: 45.67,
      status: 'pending',
    },
    {
      id: 'ORD-155',
      customer: 'Mike Chen',
      date: '2026-05-08',
      items: 5,
      total: 82.34,
      status: 'preparing',
    },
    {
      id: 'ORD-154',
      customer: 'Emma Wilson',
      date: '2026-05-07',
      items: 2,
      total: 28.99,
      status: 'shipped',
    },
    {
      id: 'ORD-153',
      customer: 'David Lee',
      date: '2026-05-07',
      items: 4,
      total: 56.78,
      status: 'delivered',
    },
  ];

  return (
    <div>
      <div className="mb-8">
        <h1>Orders</h1>
        <p className="text-muted-foreground">Manage your customer orders</p>
      </div>

      {/* Orders Table */}
      <Card>
        <CardContent className="p-0">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="border-b border-border bg-secondary/50">
                <tr>
                  <th className="text-left p-4">Order ID</th>
                  <th className="text-left p-4">Customer</th>
                  <th className="text-left p-4">Date</th>
                  <th className="text-left p-4">Items</th>
                  <th className="text-left p-4">Total</th>
                  <th className="text-left p-4">Status</th>
                  <th className="text-left p-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                {orders.map((order) => (
                  <tr key={order.id} className="border-b border-border last:border-0">
                    <td className="p-4">{order.id}</td>
                    <td className="p-4">{order.customer}</td>
                    <td className="p-4 text-muted-foreground">{order.date}</td>
                    <td className="p-4">{order.items} items</td>
                    <td className="p-4 text-[var(--green-primary)]">
                      {formatCurrency(order.total)}
                    </td>
                    <td className="p-4">
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
                    </td>
                    <td className="p-4">
                      <div className="flex gap-2">
                        <Button variant="outline" size="sm">
                          <Eye className="h-4 w-4" />
                        </Button>
                        {order.status === 'pending' && (
                          <Button variant="primary" size="sm">
                            Accept
                          </Button>
                        )}
                        {order.status === 'preparing' && (
                          <Button variant="primary" size="sm">
                            Ship
                          </Button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
