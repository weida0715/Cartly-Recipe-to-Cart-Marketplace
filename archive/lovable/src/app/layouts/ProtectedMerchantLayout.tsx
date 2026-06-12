import { ProtectedRoute } from '../components/ProtectedRoute';
import { MerchantLayout } from './MerchantLayout';

export function ProtectedMerchantLayout() {
  return (
    <ProtectedRoute requiredRole="merchant">
      <MerchantLayout />
    </ProtectedRoute>
  );
}
