import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { TrendingUp, Package, DollarSign, ShoppingBag } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LineChart, Line } from 'recharts';
import { useAuth } from '../../contexts/AuthContext';

export function MerchantDashboard() {
  const { user } = useAuth();
  const stats = [
    {
      title: 'Total Revenue',
      value: '$12,458',
      change: '+12.5%',
      icon: DollarSign,
      color: 'text-[var(--green-primary)]',
    },
    {
      title: 'Total Orders',
      value: '156',
      change: '+8.2%',
      icon: ShoppingBag,
      color: 'text-[var(--orange-accent)]',
    },
    {
      title: 'Products',
      value: '42',
      change: '+3',
      icon: Package,
      color: 'text-blue-500',
    },
    {
      title: 'Avg Order Value',
      value: '$79.86',
      change: '+4.3%',
      icon: TrendingUp,
      color: 'text-purple-500',
    },
  ];

  const weeklyData = [
    { day: 'Mon', sales: 1200, orders: 15 },
    { day: 'Tue', sales: 1800, orders: 22 },
    { day: 'Wed', sales: 2100, orders: 28 },
    { day: 'Thu', sales: 1600, orders: 19 },
    { day: 'Fri', sales: 2400, orders: 31 },
    { day: 'Sat', sales: 3200, orders: 42 },
    { day: 'Sun', sales: 2800, orders: 35 },
  ];

  return (
    <div>
      <div className="mb-8">
        <h1>Merchant Dashboard</h1>
        <p className="text-muted-foreground">
          Welcome back{user?.username ? `, ${user.username}` : ''}!
        </p>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {stats.map((stat) => {
          const Icon = stat.icon;
          return (
            <Card key={stat.title}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between mb-4">
                  <div className={`w-12 h-12 rounded-full bg-secondary flex items-center justify-center ${stat.color}`}>
                    <Icon className="h-6 w-6" />
                  </div>
                  <span className="text-sm text-[var(--green-primary)]">{stat.change}</span>
                </div>
                <div className="text-sm text-muted-foreground mb-1">{stat.title}</div>
                <div className="text-2xl">{stat.value}</div>
              </CardContent>
            </Card>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {/* Sales Chart */}
        <Card>
          <CardHeader>
            <CardTitle>Weekly Sales</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={weeklyData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                <XAxis dataKey="day" />
                <YAxis />
                <Tooltip />
                <Bar dataKey="sales" fill="var(--green-primary)" radius={[8, 8, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        {/* Orders Chart */}
        <Card>
          <CardHeader>
            <CardTitle>Orders Trend</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <LineChart data={weeklyData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                <XAxis dataKey="day" />
                <YAxis />
                <Tooltip />
                <Line
                  type="monotone"
                  dataKey="orders"
                  stroke="var(--orange-accent)"
                  strokeWidth={2}
                  dot={{ fill: 'var(--orange-accent)', r: 4 }}
                />
              </LineChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      {/* Recent Orders */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Orders</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {[
              { id: 'ORD-156', customer: 'Sarah Johnson', items: 3, total: 45.67, status: 'pending' },
              { id: 'ORD-155', customer: 'Mike Chen', items: 5, total: 82.34, status: 'preparing' },
              { id: 'ORD-154', customer: 'Emma Wilson', items: 2, total: 28.99, status: 'shipped' },
            ].map((order) => (
              <div
                key={order.id}
                className="flex items-center justify-between pb-4 border-b border-border last:border-0"
              >
                <div>
                  <div className="mb-1">{order.id}</div>
                  <div className="text-sm text-muted-foreground">{order.customer}</div>
                </div>
                <div className="text-right">
                  <div className="mb-1">${order.total.toFixed(2)}</div>
                  <div className="text-sm text-muted-foreground">{order.items} items</div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
