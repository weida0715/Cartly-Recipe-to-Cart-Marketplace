import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { Users, Store, DollarSign, ShoppingBag } from 'lucide-react';
import { LineChart, Line, BarChart, Bar, PieChart, Pie, Cell, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import { useAuth } from '../../contexts/AuthContext';

export function AdminDashboard() {
  const { user } = useAuth();
  const stats = [
    {
      title: 'Total Users',
      value: '12,458',
      change: '+12.5%',
      icon: Users,
      color: 'text-blue-500',
    },
    {
      title: 'Active Merchants',
      value: '156',
      change: '+8.2%',
      icon: Store,
      color: 'text-[var(--green-primary)]',
    },
    {
      title: 'Platform Revenue',
      value: '$245,678',
      change: '+18.3%',
      icon: DollarSign,
      color: 'text-[var(--orange-accent)]',
    },
    {
      title: 'Total Orders',
      value: '8,234',
      change: '+15.7%',
      icon: ShoppingBag,
      color: 'text-purple-500',
    },
  ];

  const monthlyData = [
    { month: 'Jan', users: 400, merchants: 20, revenue: 45000 },
    { month: 'Feb', users: 520, merchants: 28, revenue: 52000 },
    { month: 'Mar', users: 680, merchants: 35, revenue: 68000 },
    { month: 'Apr', users: 890, merchants: 42, revenue: 89000 },
    { month: 'May', users: 1200, merchants: 56, revenue: 125000 },
  ];

  const categoryData = [
    { name: 'Vegetables', value: 35 },
    { name: 'Fruits', value: 25 },
    { name: 'Meat', value: 20 },
    { name: 'Dairy', value: 12 },
    { name: 'Others', value: 8 },
  ];

  const COLORS = ['#4CAF50', '#FF9800', '#2196F3', '#FFC107', '#9C27B0'];

  return (
    <div>
      <div className="mb-8">
        <h1>Admin Dashboard</h1>
        <p className="text-muted-foreground">
          Welcome{user?.username ? `, ${user.username}` : ''}! Platform overview and analytics
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
        {/* Platform Growth */}
        <Card>
          <CardHeader>
            <CardTitle>Platform Growth</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <LineChart data={monthlyData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Line type="monotone" dataKey="users" stroke="#4CAF50" strokeWidth={2} name="Users" />
                <Line type="monotone" dataKey="merchants" stroke="#FF9800" strokeWidth={2} name="Merchants" />
              </LineChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        {/* Revenue Trend */}
        <Card>
          <CardHeader>
            <CardTitle>Monthly Revenue</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={monthlyData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip />
                <Bar dataKey="revenue" fill="var(--green-primary)" radius={[8, 8, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Category Distribution */}
        <Card>
          <CardHeader>
            <CardTitle>Product Categories</CardTitle>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <PieChart>
                <Pie
                  data={categoryData}
                  cx="50%"
                  cy="50%"
                  labelLine={false}
                  label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                  outerRadius={100}
                  fill="#8884d8"
                  dataKey="value"
                >
                  {categoryData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        {/* Pending Approvals */}
        <Card>
          <CardHeader>
            <CardTitle>Pending Merchant Approvals</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {[
                { name: 'Green Valley Grocers', date: '2026-05-07', products: 25 },
                { name: 'Ocean Fresh Seafood', date: '2026-05-06', products: 18 },
                { name: 'Mountain Herbs', date: '2026-05-05', products: 32 },
              ].map((merchant, idx) => (
                <div
                  key={idx}
                  className="flex items-center justify-between pb-4 border-b border-border last:border-0"
                >
                  <div>
                    <div className="mb-1">{merchant.name}</div>
                    <div className="text-sm text-muted-foreground">
                      Applied: {merchant.date} • {merchant.products} products
                    </div>
                  </div>
                  <div className="flex gap-2">
                    <button className="px-3 py-1 text-sm bg-[var(--green-primary)] text-white rounded hover:bg-[var(--green-dark)]">
                      Approve
                    </button>
                    <button className="px-3 py-1 text-sm border border-border rounded hover:bg-secondary">
                      Review
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
