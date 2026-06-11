import { Link } from 'react-router';
import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Badge } from '../../components/ui/Badge';
import { User, MapPin, CreditCard, Heart, Package, Store } from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';

export function CustomerDashboard() {
  const { user } = useAuth();

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="mb-8">
        <h1>My Account</h1>
        {user && (
          <div className="flex items-center gap-2 mt-2">
            <Badge variant={user.role === 'admin' ? 'default' : user.role === 'merchant' ? 'warning' : 'secondary'}>
              {user.role}
            </Badge>
          </div>
        )}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Profile Card */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5" />
              Profile Information
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <div className="text-sm text-muted-foreground">Username</div>
              <div>{user?.username || 'Not set'}</div>
            </div>
            <div>
              <div className="text-sm text-muted-foreground">Email</div>
              <div>{user?.email || 'Not set'}</div>
            </div>
            <div>
              <div className="text-sm text-muted-foreground">Role</div>
              <div className="capitalize">{user?.role || 'customer'}</div>
            </div>
            <div>
              <div className="text-sm text-muted-foreground">Member Since</div>
              <div>May 2026</div>
            </div>
            <Button variant="outline" className="w-full">
              Edit Profile
            </Button>
          </CardContent>
        </Card>

        {/* Quick Stats */}
        <Card>
          <CardHeader>
            <CardTitle>Quick Stats</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div className="flex justify-between">
              <span className="text-muted-foreground">Total Orders</span>
              <span>12</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Saved Recipes</span>
              <span>8</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Reviews Written</span>
              <span>5</span>
            </div>
            <Link to="/orders">
              <Button variant="primary" className="w-full">
                <Package className="h-4 w-4" />
                View Order History
              </Button>
            </Link>
          </CardContent>
        </Card>

        {/* Quick Actions */}
        <Card>
          <CardHeader>
            <CardTitle>Quick Actions</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <Button variant="outline" className="w-full justify-start">
              <MapPin className="h-4 w-4" />
              Manage Addresses
            </Button>
            <Button variant="outline" className="w-full justify-start">
              <CreditCard className="h-4 w-4" />
              Payment Methods
            </Button>
            <Button variant="outline" className="w-full justify-start">
              <Heart className="h-4 w-4" />
              Saved Recipes
            </Button>
            {user?.role === 'merchant' && (
              <Link to="/merchant">
                <Button variant="accent" className="w-full justify-start">
                  <Store className="h-4 w-4" />
                  Merchant Dashboard
                </Button>
              </Link>
            )}
            {user?.role === 'customer' && (
              <Button variant="accent" className="w-full justify-start">
                <Store className="h-4 w-4" />
                Become a Merchant
              </Button>
            )}
          </CardContent>
        </Card>
      </div>

      {/* Recent Orders */}
      <Card className="mt-6">
        <CardHeader>
          <CardTitle>Recent Orders</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {[
              { id: 'ORD-001', date: '2026-05-05', status: 'delivered', total: 45.97 },
              { id: 'ORD-002', date: '2026-04-28', status: 'shipped', total: 32.45 },
              { id: 'ORD-003', date: '2026-04-20', status: 'delivered', total: 28.99 },
            ].map((order) => (
              <div
                key={order.id}
                className="flex items-center justify-between pb-4 border-b border-border last:border-0"
              >
                <div>
                  <div className="mb-1">{order.id}</div>
                  <div className="text-sm text-muted-foreground">{order.date}</div>
                </div>
                <div className="text-right">
                  <Badge
                    variant={
                      order.status === 'delivered'
                        ? 'success'
                        : order.status === 'shipped'
                        ? 'warning'
                        : 'default'
                    }
                  >
                    {order.status}
                  </Badge>
                  <div className="text-sm text-muted-foreground mt-1">
                    ${order.total.toFixed(2)}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
