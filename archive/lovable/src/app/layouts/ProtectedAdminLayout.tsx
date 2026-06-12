import { ProtectedRoute } from '../components/ProtectedRoute';
import { AdminLayout } from './AdminLayout';

export function ProtectedAdminLayout() {
  return (
    <ProtectedRoute requiredRole="admin">
      <AdminLayout />
    </ProtectedRoute>
  );
}
