import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Badge } from '../../components/ui/Badge';
import { Store, Eye, Check, X } from 'lucide-react';
import { merchants } from '../../data/mockData';
import { toast } from 'sonner';

export function MerchantApprovalPage() {
  const pendingMerchants = [
    { id: 'm5', name: 'Green Valley Grocers', logo: '🥬', description: 'Fresh organic vegetables', date: '2026-05-07', products: 25 },
    { id: 'm6', name: 'Ocean Fresh Seafood', logo: '🐟', description: 'Premium seafood and fish', date: '2026-05-06', products: 18 },
    { id: 'm7', name: 'Mountain Herbs', logo: '🌿', description: 'Natural herbs and spices', date: '2026-05-05', products: 32 },
  ];

  const handleApprove = (merchantName: string) => {
    toast.success('Merchant approved!', { description: merchantName });
  };

  const handleReject = (merchantName: string) => {
    toast.error('Merchant rejected', { description: merchantName });
  };

  return (
    <div>
      <div className="mb-8">
        <h1>Merchant Approval</h1>
        <p className="text-muted-foreground">Review and approve merchant applications</p>
      </div>

      {/* Pending Approvals */}
      <div className="mb-8">
        <h3 className="mb-4">Pending Applications ({pendingMerchants.length})</h3>
        <div className="space-y-4">
          {pendingMerchants.map((merchant) => (
            <Card key={merchant.id}>
              <CardContent className="p-6">
                <div className="flex items-start gap-4">
                  <div className="w-16 h-16 bg-[var(--green-primary)] rounded-full flex items-center justify-center text-3xl">
                    {merchant.logo}
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center justify-between mb-2">
                      <div>
                        <h4 className="mb-1">{merchant.name}</h4>
                        <p className="text-sm text-muted-foreground">{merchant.description}</p>
                      </div>
                      <Badge variant="warning">Pending</Badge>
                    </div>
                    <div className="grid grid-cols-2 gap-4 mb-4">
                      <div>
                        <div className="text-sm text-muted-foreground">Application Date</div>
                        <div>{merchant.date}</div>
                      </div>
                      <div>
                        <div className="text-sm text-muted-foreground">Products</div>
                        <div>{merchant.products} items</div>
                      </div>
                    </div>
                    <div className="flex gap-3">
                      <Button variant="primary" onClick={() => handleApprove(merchant.name)}>
                        <Check className="h-4 w-4" />
                        Approve
                      </Button>
                      <Button variant="outline" onClick={() => handleReject(merchant.name)}>
                        <X className="h-4 w-4" />
                        Reject
                      </Button>
                      <Button variant="ghost">
                        <Eye className="h-4 w-4" />
                        View Details
                      </Button>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>

      {/* Approved Merchants */}
      <div>
        <h3 className="mb-4">Approved Merchants ({merchants.length})</h3>
        <Card>
          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="border-b border-border bg-secondary/50">
                  <tr>
                    <th className="text-left p-4">Merchant</th>
                    <th className="text-left p-4">Rating</th>
                    <th className="text-left p-4">Status</th>
                    <th className="text-left p-4">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {merchants.map((merchant) => (
                    <tr key={merchant.id} className="border-b border-border last:border-0">
                      <td className="p-4">
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 bg-[var(--green-primary)] rounded-full flex items-center justify-center text-xl">
                            {merchant.logo}
                          </div>
                          <div>
                            <div>{merchant.name}</div>
                            <div className="text-sm text-muted-foreground">{merchant.description}</div>
                          </div>
                        </div>
                      </td>
                      <td className="p-4">{merchant.rating} / 5.0</td>
                      <td className="p-4">
                        <Badge variant={merchant.status === 'active' ? 'success' : 'secondary'}>
                          {merchant.status}
                        </Badge>
                      </td>
                      <td className="p-4">
                        <div className="flex gap-2">
                          <Button variant="outline" size="sm">
                            <Eye className="h-4 w-4" />
                          </Button>
                          <Button variant="ghost" size="sm" className="text-destructive">
                            Suspend
                          </Button>
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
    </div>
  );
}
