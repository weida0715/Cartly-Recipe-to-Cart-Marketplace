import { useState } from 'react';
import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Badge } from '../../components/ui/Badge';
import { Plus, Search, Edit, Trash2 } from 'lucide-react';
import { products } from '../../data/mockData';
import { formatCurrency } from '../../lib/utils';
import { toast } from 'sonner';

export function ProductManagementPage() {
  const [searchQuery, setSearchQuery] = useState('');
  const merchantProducts = products.filter((p) => p.merchantId === 'm1');

  const filteredProducts = merchantProducts.filter((p) =>
    p.name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const handleDelete = (productName: string) => {
    toast.success('Product deleted', { description: productName });
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1>Product Management</h1>
          <p className="text-muted-foreground">Manage your product inventory</p>
        </div>
        <Button variant="primary">
          <Plus className="h-5 w-5" />
          Add Product
        </Button>
      </div>

      {/* Search and Filters */}
      <Card className="mb-6">
        <CardContent className="p-4">
          <div className="flex gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                type="search"
                placeholder="Search products..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="pl-10"
              />
            </div>
            <select className="h-10 px-3 rounded-lg border border-border bg-input-background">
              <option>All Categories</option>
              <option>Vegetables</option>
              <option>Fruits</option>
              <option>Dairy</option>
            </select>
            <select className="h-10 px-3 rounded-lg border border-border bg-input-background">
              <option>All Status</option>
              <option>In Stock</option>
              <option>Low Stock</option>
              <option>Out of Stock</option>
            </select>
          </div>
        </CardContent>
      </Card>

      {/* Products Table */}
      <Card>
        <CardContent className="p-0">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="border-b border-border bg-secondary/50">
                <tr>
                  <th className="text-left p-4">Product</th>
                  <th className="text-left p-4">Category</th>
                  <th className="text-left p-4">Price</th>
                  <th className="text-left p-4">Stock</th>
                  <th className="text-left p-4">Status</th>
                  <th className="text-left p-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                {filteredProducts.map((product) => (
                  <tr key={product.id} className="border-b border-border last:border-0">
                    <td className="p-4">
                      <div className="flex items-center gap-3">
                        <div className="w-12 h-12 bg-gradient-to-br from-secondary to-accent/20 rounded flex items-center justify-center text-2xl">
                          {product.image}
                        </div>
                        <div>
                          <div>{product.name}</div>
                          <div className="text-sm text-muted-foreground">{product.unit}</div>
                        </div>
                      </div>
                    </td>
                    <td className="p-4">{product.category}</td>
                    <td className="p-4 text-[var(--green-primary)]">
                      {formatCurrency(product.price)}
                    </td>
                    <td className="p-4">{product.stock}</td>
                    <td className="p-4">
                      <Badge
                        variant={
                          product.stock > 20
                            ? 'success'
                            : product.stock > 0
                            ? 'warning'
                            : 'danger'
                        }
                      >
                        {product.stock > 20 ? 'In Stock' : product.stock > 0 ? 'Low Stock' : 'Out of Stock'}
                      </Badge>
                    </td>
                    <td className="p-4">
                      <div className="flex gap-2">
                        <Button variant="ghost" size="sm">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="sm"
                          className="text-destructive hover:text-destructive"
                          onClick={() => handleDelete(product.name)}
                        >
                          <Trash2 className="h-4 w-4" />
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
  );
}
