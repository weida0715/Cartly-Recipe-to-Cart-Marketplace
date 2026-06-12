import { useState } from 'react';
import { Link, useNavigate } from 'react-router';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Badge } from '../../components/ui/Badge';
import { toast } from 'sonner';
import { useAuth } from '../../contexts/AuthContext';

export function RegisterPage() {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [formData, setFormData] = useState({
    username: '',
    email: '',
    password: '',
    confirmPassword: '',
  });

  const handleRegister = (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.password !== formData.confirmPassword) {
      toast.error('Passwords do not match');
      return;
    }

    login(formData.username, formData.email);

    const usernameLower = formData.username.toLowerCase();
    if (usernameLower === 'admin') {
      toast.success('Account created as Admin');
      navigate('/admin');
    } else if (usernameLower === 'merchant') {
      toast.success('Account created as Merchant');
      navigate('/merchant');
    } else {
      toast.success('Account created as Customer');
      navigate('/');
    }
  };

  return (
    <div className="min-h-screen bg-[var(--gray-bg)] flex items-center justify-center px-4">
      <Card className="w-full max-w-md">
        <CardHeader>
          <div className="text-center mb-4">
            <Link to="/" className="inline-flex items-center gap-2">
              <div className="text-3xl">🛒</div>
              <span className="text-2xl text-[var(--green-primary)]">Cartly</span>
            </Link>
          </div>
          <CardTitle className="text-center">Create Account</CardTitle>
          <CardDescription className="text-center mt-2">
            <div className="space-y-1 text-xs">
              <div>Username determines role:</div>
              <div className="flex gap-2 justify-center flex-wrap">
                <Badge variant="default">admin</Badge>
                <Badge variant="warning">merchant</Badge>
                <Badge variant="secondary">customer</Badge>
              </div>
            </div>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleRegister} className="space-y-4">
            <div>
              <label className="text-sm mb-2 block">Username</label>
              <Input
                type="text"
                placeholder="admin / merchant / customer"
                value={formData.username}
                onChange={(e) => setFormData({ ...formData, username: e.target.value })}
                required
              />
            </div>
            <div>
              <label className="text-sm mb-2 block">Email</label>
              <Input
                type="email"
                placeholder="you@example.com"
                value={formData.email}
                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                required
              />
            </div>
            <div>
              <label className="text-sm mb-2 block">Password</label>
              <Input
                type="password"
                placeholder="Enter your password"
                value={formData.password}
                onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                required
              />
            </div>
            <div>
              <label className="text-sm mb-2 block">Confirm Password</label>
              <Input
                type="password"
                placeholder="Confirm your password"
                value={formData.confirmPassword}
                onChange={(e) => setFormData({ ...formData, confirmPassword: e.target.value })}
                required
              />
            </div>
            <Button type="submit" variant="primary" className="w-full" size="lg">
              Register
            </Button>
            <div className="text-center text-sm">
              Already have an account?{' '}
              <Link to="/auth/login" className="text-[var(--green-primary)] hover:underline">
                Login
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
