import { Outlet, Link, useLocation, useNavigate } from 'react-router';
import { LayoutDashboard, Users, Store, FolderTree, Flag, LogOut, Home } from 'lucide-react';
import { Badge } from '../components/ui/Badge';
import { useAuth } from '../contexts/AuthContext';
import { toast } from 'sonner';

export function AdminLayout() {
  const location = useLocation();
  const navigate = useNavigate();
  const { user, logout } = useAuth();

  const navItems = [
    { path: '/admin', icon: LayoutDashboard, label: 'Dashboard' },
    { path: '/admin/merchants', icon: Store, label: 'Merchant Approval' },
    { path: '/admin/users', icon: Users, label: 'User Management' },
    { path: '/admin/categories', icon: FolderTree, label: 'Categories' },
    { path: '/admin/moderation', icon: Flag, label: 'Content Moderation' },
  ];

  return (
    <div className="flex h-screen bg-[var(--gray-bg)]">
      {/* Sidebar */}
      <aside className="w-64 bg-white border-r border-border">
        <div className="p-6 border-b border-border">
          <Link to="/" className="flex items-center gap-2">
            <div className="text-2xl">🛒</div>
            <div className="flex-1">
              <div className="text-xl text-[var(--green-primary)]">Cartly</div>
              <div className="text-xs text-muted-foreground">Admin Portal</div>
            </div>
          </Link>
          {user && (
            <div className="mt-3 pt-3 border-t border-border">
              <div className="text-sm">{user.username}</div>
              <Badge variant="default" className="mt-1">{user.role}</Badge>
            </div>
          )}
        </div>
        <nav className="p-4 space-y-2">
          {navItems.map((item) => {
            const Icon = item.icon;
            const isActive = location.pathname === item.path;
            return (
              <Link
                key={item.path}
                to={item.path}
                className={`flex items-center gap-3 px-4 py-3 rounded-lg transition-colors ${
                  isActive
                    ? 'bg-[var(--green-primary)] text-white'
                    : 'hover:bg-secondary text-foreground'
                }`}
              >
                <Icon className="h-5 w-5" />
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>
        <div className="absolute bottom-0 w-64 p-4 border-t border-border bg-white space-y-2">
          <Link
            to="/"
            className="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-secondary transition-colors"
          >
            <Home className="h-5 w-5" />
            <span>Customer View</span>
          </Link>
          <button
            onClick={() => {
              logout();
              toast.success('Logged out successfully');
              navigate('/');
            }}
            className="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-secondary transition-colors text-destructive"
          >
            <LogOut className="h-5 w-5" />
            <span>Logout</span>
          </button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-auto">
        <div className="p-8">
          <Outlet />
        </div>
      </main>
    </div>
  );
}
