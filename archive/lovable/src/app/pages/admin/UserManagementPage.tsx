import { useState } from 'react';
import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Badge } from '../../components/ui/Badge';
import { Search, Eye, Ban } from 'lucide-react';
import { toast } from 'sonner';

export function UserManagementPage() {
  const [searchQuery, setSearchQuery] = useState('');

  const users = [
    { id: '1', name: 'Sarah Johnson', email: 'sarah@example.com', role: 'customer', status: 'active', joined: '2026-04-15', orders: 12 },
    { id: '2', name: 'Mike Chen', email: 'mike@example.com', role: 'customer', status: 'active', joined: '2026-03-20', orders: 8 },
    { id: '3', name: 'Emma Wilson', email: 'emma@example.com', role: 'merchant', status: 'active', joined: '2026-02-10', orders: 156 },
    { id: '4', name: 'David Lee', email: 'david@example.com', role: 'customer', status: 'suspended', joined: '2026-01-05', orders: 3 },
  ];

  const filteredUsers = users.filter(
    (user) =>
      user.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const handleSuspend = (userName: string) => {
    toast.success('User suspended', { description: userName });
  };

  return (
    <div>
      <div className="mb-8">
        <h1>User Management</h1>
        <p className="text-muted-foreground">Manage platform users and accounts</p>
      </div>

      {/* Search */}
      <Card className="mb-6">
        <CardContent className="p-4">
          <div className="flex gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                type="search"
                placeholder="Search users by name or email..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="pl-10"
              />
            </div>
            <select className="h-10 px-3 rounded-lg border border-border bg-input-background">
              <option>All Roles</option>
              <option>Customer</option>
              <option>Merchant</option>
              <option>Admin</option>
            </select>
            <select className="h-10 px-3 rounded-lg border border-border bg-input-background">
              <option>All Status</option>
              <option>Active</option>
              <option>Suspended</option>
            </select>
          </div>
        </CardContent>
      </Card>

      {/* Users Table */}
      <Card>
        <CardContent className="p-0">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="border-b border-border bg-secondary/50">
                <tr>
                  <th className="text-left p-4">User</th>
                  <th className="text-left p-4">Role</th>
                  <th className="text-left p-4">Status</th>
                  <th className="text-left p-4">Joined</th>
                  <th className="text-left p-4">Orders</th>
                  <th className="text-left p-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                {filteredUsers.map((user) => (
                  <tr key={user.id} className="border-b border-border last:border-0">
                    <td className="p-4">
                      <div>
                        <div>{user.name}</div>
                        <div className="text-sm text-muted-foreground">{user.email}</div>
                      </div>
                    </td>
                    <td className="p-4">
                      <Badge variant={user.role === 'merchant' ? 'warning' : 'secondary'}>
                        {user.role}
                      </Badge>
                    </td>
                    <td className="p-4">
                      <Badge variant={user.status === 'active' ? 'success' : 'danger'}>
                        {user.status}
                      </Badge>
                    </td>
                    <td className="p-4 text-muted-foreground">{user.joined}</td>
                    <td className="p-4">{user.orders}</td>
                    <td className="p-4">
                      <div className="flex gap-2">
                        <Button variant="outline" size="sm">
                          <Eye className="h-4 w-4" />
                        </Button>
                        {user.status === 'active' && (
                          <Button
                            variant="ghost"
                            size="sm"
                            className="text-destructive"
                            onClick={() => handleSuspend(user.name)}
                          >
                            <Ban className="h-4 w-4" />
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
