import { createContext, useContext, useState, ReactNode, useEffect } from 'react';

export type UserRole = 'customer' | 'merchant' | 'admin';

export interface User {
  username: string;
  email: string;
  role: UserRole;
}

interface AuthContextType {
  user: User | null;
  login: (username: string, email: string) => void;
  logout: () => void;
  isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(() => {
    const stored = localStorage.getItem('cartly_user');
    return stored ? JSON.parse(stored) : null;
  });

  useEffect(() => {
    if (user) {
      localStorage.setItem('cartly_user', JSON.stringify(user));
    } else {
      localStorage.removeItem('cartly_user');
    }
  }, [user]);

  const login = (username: string, email: string) => {
    let role: UserRole = 'customer';

    if (username.toLowerCase() === 'admin') {
      role = 'admin';
    } else if (username.toLowerCase() === 'merchant') {
      role = 'merchant';
    } else {
      role = 'customer';
    }

    const newUser: User = {
      username,
      email,
      role,
    };

    setUser(newUser);
  };

  const logout = () => {
    setUser(null);
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        login,
        logout,
        isAuthenticated: !!user,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
