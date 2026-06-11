import { Outlet, Link, useLocation, useNavigate } from 'react-router';
import { ShoppingCart, User, Search, ChefHat, Home, Package, LogOut, LayoutDashboard, Store } from 'lucide-react';
import { Button } from '../components/ui/Button';
import { Input } from '../components/ui/Input';
import { Badge } from '../components/ui/Badge';
import { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import * as DropdownMenu from '@radix-ui/react-dropdown-menu';
import { toast } from 'sonner';

export function CustomerLayout() {
  const location = useLocation();
  const navigate = useNavigate();
  const { user, logout, isAuthenticated } = useAuth();
  const [cartCount] = useState(3);

  return (
    <div className="min-h-screen bg-[var(--gray-bg)]">
      {/* Navigation Bar */}
      <nav className="bg-white border-b border-border sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            {/* Logo */}
            <Link to="/" className="flex items-center gap-2">
              <div className="text-2xl">🛒</div>
              <span className="text-xl text-[var(--green-primary)]">Cartly</span>
            </Link>

            {/* Search Bar */}
            <div className="hidden md:flex flex-1 max-w-lg mx-8">
              <div className="relative w-full">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  type="search"
                  placeholder="Search products or recipes..."
                  className="pl-10"
                />
              </div>
            </div>

            {/* Navigation Links */}
            <div className="flex items-center gap-6">
              <Link
                to="/"
                className={`flex items-center gap-1 hover:text-[var(--green-primary)] transition-colors ${
                  location.pathname === '/' ? 'text-[var(--green-primary)]' : ''
                }`}
              >
                <Home className="h-5 w-5" />
                <span className="hidden lg:inline">Home</span>
              </Link>
              <Link
                to="/marketplace"
                className={`flex items-center gap-1 hover:text-[var(--green-primary)] transition-colors ${
                  location.pathname === '/marketplace' ? 'text-[var(--green-primary)]' : ''
                }`}
              >
                <Package className="h-5 w-5" />
                <span className="hidden lg:inline">Marketplace</span>
              </Link>
              <Link
                to="/recipes"
                className={`flex items-center gap-1 hover:text-[var(--green-primary)] transition-colors ${
                  location.pathname === '/recipes' ? 'text-[var(--green-primary)]' : ''
                }`}
              >
                <ChefHat className="h-5 w-5" />
                <span className="hidden lg:inline">Recipes</span>
              </Link>
              <Link to="/cart" className="relative">
                <ShoppingCart className="h-6 w-6 hover:text-[var(--green-primary)] transition-colors" />
                {cartCount > 0 && (
                  <span className="absolute -top-2 -right-2 bg-[var(--orange-accent)] text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {cartCount}
                  </span>
                )}
              </Link>
              {isAuthenticated ? (
                <DropdownMenu.Root>
                  <DropdownMenu.Trigger asChild>
                    <Button variant="ghost" size="sm" className="flex items-center gap-2">
                      <User className="h-5 w-5" />
                      <span className="hidden lg:inline">{user?.username}</span>
                      {user?.role && (
                        <Badge variant={user.role === 'admin' ? 'default' : user.role === 'merchant' ? 'warning' : 'secondary'} className="hidden lg:inline-flex">
                          {user.role}
                        </Badge>
                      )}
                    </Button>
                  </DropdownMenu.Trigger>
                  <DropdownMenu.Portal>
                    <DropdownMenu.Content className="min-w-[200px] bg-white rounded-lg shadow-lg border border-border p-1 z-50">
                      <DropdownMenu.Item
                        className="flex items-center gap-2 px-3 py-2 rounded hover:bg-secondary cursor-pointer outline-none"
                        onSelect={() => navigate('/dashboard')}
                      >
                        <User className="h-4 w-4" />
                        My Account
                      </DropdownMenu.Item>
                      {user?.role === 'merchant' && (
                        <DropdownMenu.Item
                          className="flex items-center gap-2 px-3 py-2 rounded hover:bg-secondary cursor-pointer outline-none"
                          onSelect={() => navigate('/merchant')}
                        >
                          <Store className="h-4 w-4" />
                          Merchant Dashboard
                        </DropdownMenu.Item>
                      )}
                      {user?.role === 'admin' && (
                        <DropdownMenu.Item
                          className="flex items-center gap-2 px-3 py-2 rounded hover:bg-secondary cursor-pointer outline-none"
                          onSelect={() => navigate('/admin')}
                        >
                          <LayoutDashboard className="h-4 w-4" />
                          Admin Dashboard
                        </DropdownMenu.Item>
                      )}
                      <DropdownMenu.Separator className="h-px bg-border my-1" />
                      <DropdownMenu.Item
                        className="flex items-center gap-2 px-3 py-2 rounded hover:bg-secondary cursor-pointer outline-none text-destructive"
                        onSelect={() => {
                          logout();
                          toast.success('Logged out successfully');
                          navigate('/');
                        }}
                      >
                        <LogOut className="h-4 w-4" />
                        Logout
                      </DropdownMenu.Item>
                    </DropdownMenu.Content>
                  </DropdownMenu.Portal>
                </DropdownMenu.Root>
              ) : (
                <Link to="/auth/login">
                  <Button variant="primary" size="sm">
                    Login
                  </Button>
                </Link>
              )}
            </div>
          </div>
        </div>
      </nav>

      {/* Page Content */}
      <main>
        <Outlet />
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-border mt-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
              <h4 className="mb-4">About Cartly</h4>
              <p className="text-sm text-muted-foreground">
                Your one-stop marketplace for fresh groceries and delicious recipes.
              </p>
            </div>
            <div>
              <h4 className="mb-4">Customer Service</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li><Link to="#" className="hover:text-[var(--green-primary)]">Help Center</Link></li>
                <li><Link to="#" className="hover:text-[var(--green-primary)]">Track Order</Link></li>
                <li><Link to="#" className="hover:text-[var(--green-primary)]">Returns</Link></li>
              </ul>
            </div>
            {!isAuthenticated || user?.role === 'customer' ? (
              <div>
                <h4 className="mb-4">For Merchants</h4>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li><Link to="#" className="hover:text-[var(--green-primary)]">Become a Merchant</Link></li>
                </ul>
              </div>
            ) : user?.role === 'merchant' ? (
              <div>
                <h4 className="mb-4">Merchant</h4>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li><Link to="/merchant" className="hover:text-[var(--green-primary)]">Merchant Dashboard</Link></li>
                  <li><Link to="/merchant/products" className="hover:text-[var(--green-primary)]">Manage Products</Link></li>
                </ul>
              </div>
            ) : (
              <div>
                <h4 className="mb-4">Administration</h4>
                <ul className="space-y-2 text-sm text-muted-foreground">
                  <li><Link to="/admin" className="hover:text-[var(--green-primary)]">Admin Dashboard</Link></li>
                </ul>
              </div>
            )}
            <div>
              <h4 className="mb-4">Connect</h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li><Link to="#" className="hover:text-[var(--green-primary)]">Facebook</Link></li>
                <li><Link to="#" className="hover:text-[var(--green-primary)]">Instagram</Link></li>
                <li><Link to="#" className="hover:text-[var(--green-primary)]">Twitter</Link></li>
              </ul>
            </div>
          </div>
          <div className="border-t border-border mt-8 pt-8 text-center text-sm text-muted-foreground">
            © 2026 Cartly. All rights reserved.
          </div>
        </div>
      </footer>
    </div>
  );
}
