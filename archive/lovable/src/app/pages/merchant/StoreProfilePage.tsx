import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Badge } from '../../components/ui/Badge';
import { Store, Star } from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';

export function StoreProfilePage() {
  const { user } = useAuth();

  return (
    <div>
      <div className="mb-8">
        <h1>Store Profile</h1>
        <p className="text-muted-foreground">Manage your store information</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Store Info */}
        <div className="lg:col-span-2 space-y-6">
          <Card>
            <CardContent className="p-6">
              <h3 className="mb-4">Store Information</h3>
              <div className="space-y-4">
                <div>
                  <label className="text-sm mb-2 block">Store Name</label>
                  <Input type="text" defaultValue="Fresh Farms Market" />
                </div>
                <div>
                  <label className="text-sm mb-2 block">Description</label>
                  <textarea
                    className="w-full min-h-[100px] px-3 py-2 rounded-lg border border-border bg-input-background"
                    defaultValue="Premium organic produce and dairy products"
                  />
                </div>
                <div>
                  <label className="text-sm mb-2 block">Store Logo (Emoji)</label>
                  <Input type="text" defaultValue="🌾" maxLength={2} />
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm mb-2 block">Email</label>
                    <Input type="email" defaultValue={user?.email || 'contact@freshfarms.com'} />
                  </div>
                  <div>
                    <label className="text-sm mb-2 block">Phone</label>
                    <Input type="tel" defaultValue="+1 (555) 123-4567" />
                  </div>
                </div>
                <div>
                  <label className="text-sm mb-2 block">Address</label>
                  <Input type="text" defaultValue="123 Farm Road, San Francisco, CA 94102" />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <h3 className="mb-4">Operating Hours</h3>
              <div className="space-y-3">
                {['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'].map((day) => (
                  <div key={day} className="flex items-center gap-4">
                    <div className="w-24">{day}</div>
                    <Input type="time" defaultValue="09:00" className="flex-1" />
                    <span>to</span>
                    <Input type="time" defaultValue="18:00" className="flex-1" />
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          <Button variant="primary" size="lg">
            Save Changes
          </Button>
        </div>

        {/* Store Stats */}
        <div className="space-y-6">
          <Card>
            <CardContent className="p-6 text-center">
              <div className="w-20 h-20 bg-[var(--green-primary)] rounded-full flex items-center justify-center mx-auto mb-4 text-4xl text-white">
                🌾
              </div>
              <h3 className="mb-2">Fresh Farms Market</h3>
              <div className="flex items-center justify-center gap-1 mb-4">
                <Star className="h-4 w-4 fill-[var(--yellow-accent)] text-[var(--yellow-accent)]" />
                <span>4.8</span>
                <span className="text-muted-foreground">(245 reviews)</span>
              </div>
              <Badge variant="success">Active</Badge>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <h4 className="mb-4">Store Statistics</h4>
              <div className="space-y-3">
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Total Products</span>
                  <span>42</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Total Orders</span>
                  <span>156</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Member Since</span>
                  <span>Jan 2025</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Rating</span>
                  <span>4.8 / 5.0</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <h4 className="mb-4">Account Status</h4>
              <Button variant="outline" className="w-full mb-2">
                View Public Store
              </Button>
              <Button variant="ghost" className="w-full text-destructive">
                Deactivate Store
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
