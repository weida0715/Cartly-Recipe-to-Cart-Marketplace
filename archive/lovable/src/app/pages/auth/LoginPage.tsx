import { useState } from 'react';
import { Link, useNavigate } from 'react-router';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Badge } from '../../components/ui/Badge';
import { toast } from 'sonner';
import { useAuth } from '../../contexts/AuthContext';

export function LoginPage() {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [username, setUsername] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();

    login(username, email);

    const usernameLower = username.toLowerCase();
    if (usernameLower === 'admin') {
      toast.success('Logged in as Admin');
      navigate('/admin');
    } else if (usernameLower === 'merchant') {
      toast.success('Logged in as Merchant');
      navigate('/merchant');
    } else {
      toast.success('Logged in as Customer');
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
          <CardTitle className="text-center">Welcome Back</CardTitle>
          <CardDescription className="text-center mt-2">
            <div className="space-y-1 text-xs">
              <div>Demo logins:</div>
              <div className="flex gap-2 justify-center flex-wrap">
                <Badge variant="default">admin</Badge>
                <Badge variant="warning">merchant</Badge>
                <Badge variant="secondary">customer</Badge>
              </div>
            </div>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleLogin} className="space-y-4">
            <div>
              <label className="text-sm mb-2 block">Username</label>
              <Input
                type="text"
                placeholder="admin / merchant / customer"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                required
              />
            </div>
            <div>
              <label className="text-sm mb-2 block">Email</label>
              <Input
                type="email"
                placeholder="you@example.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>
            <div>
              <label className="text-sm mb-2 block">Password</label>
              <Input
                type="password"
                placeholder="Enter your password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
            <div className="flex items-center justify-between text-sm">
              <label className="flex items-center gap-2">
                <input type="checkbox" className="rounded" />
                Remember me
              </label>
              <Link
                to="/auth/forgot-password"
                className="text-[var(--green-primary)] hover:underline"
              >
                Forgot password?
              </Link>
            </div>
            <Button type="submit" variant="primary" className="w-full" size="lg">
              Login
            </Button>
            <div className="text-center text-sm">
              Don't have an account?{' '}
              <Link to="/auth/register" className="text-[var(--green-primary)] hover:underline">
                Register
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
