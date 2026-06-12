import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Badge } from '../../components/ui/Badge';
import { Plus, Tag } from 'lucide-react';

export function VoucherManagementPage() {
  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1>Voucher Management</h1>
          <p className="text-muted-foreground">Create and manage discount vouchers</p>
        </div>
        <Button variant="primary">
          <Plus className="h-5 w-5" />
          Create Voucher
        </Button>
      </div>

      {/* Create Voucher Form */}
      <Card className="mb-6">
        <CardContent className="p-6">
          <h3 className="mb-4">Create New Voucher</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="text-sm mb-2 block">Voucher Code</label>
              <Input type="text" placeholder="e.g., SAVE20" />
            </div>
            <div>
              <label className="text-sm mb-2 block">Discount Type</label>
              <select className="w-full h-10 px-3 rounded-lg border border-border bg-input-background">
                <option>Percentage</option>
                <option>Fixed Amount</option>
              </select>
            </div>
            <div>
              <label className="text-sm mb-2 block">Discount Value</label>
              <Input type="number" placeholder="e.g., 20" />
            </div>
            <div>
              <label className="text-sm mb-2 block">Minimum Purchase</label>
              <Input type="number" placeholder="e.g., 50" />
            </div>
            <div>
              <label className="text-sm mb-2 block">Expiry Date</label>
              <Input type="date" />
            </div>
            <div>
              <label className="text-sm mb-2 block">Usage Limit</label>
              <Input type="number" placeholder="e.g., 100" />
            </div>
          </div>
          <Button variant="primary" className="mt-4">
            Create Voucher
          </Button>
        </CardContent>
      </Card>

      {/* Active Vouchers */}
      <Card>
        <CardContent className="p-6">
          <h3 className="mb-4">Active Vouchers</h3>
          <div className="space-y-4">
            {[
              { code: 'SAVE20', discount: '20%', minPurchase: 50, uses: 45, limit: 100, expiry: '2026-06-01' },
              { code: 'FIRST10', discount: '$10', minPurchase: 30, uses: 23, limit: 50, expiry: '2026-05-31' },
            ].map((voucher) => (
              <div
                key={voucher.code}
                className="flex items-center justify-between p-4 border border-border rounded-lg"
              >
                <div className="flex items-center gap-4">
                  <div className="w-12 h-12 bg-[var(--orange-accent)] rounded flex items-center justify-center text-white">
                    <Tag className="h-6 w-6" />
                  </div>
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <span>{voucher.code}</span>
                      <Badge variant="success">Active</Badge>
                    </div>
                    <div className="text-sm text-muted-foreground">
                      {voucher.discount} off • Min: ${voucher.minPurchase} • Expires: {voucher.expiry}
                    </div>
                  </div>
                </div>
                <div className="text-right">
                  <div className="text-sm text-muted-foreground mb-1">
                    {voucher.uses} / {voucher.limit} uses
                  </div>
                  <div className="flex gap-2">
                    <Button variant="outline" size="sm">
                      Edit
                    </Button>
                    <Button variant="ghost" size="sm" className="text-destructive">
                      Deactivate
                    </Button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
