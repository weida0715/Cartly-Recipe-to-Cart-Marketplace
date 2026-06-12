import { Card, CardContent } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Plus, Edit, Trash2 } from 'lucide-react';
import { categories } from '../../data/mockData';
import { toast } from 'sonner';

export function CategoryManagementPage() {
  const handleDelete = (categoryName: string) => {
    toast.success('Category deleted', { description: categoryName });
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1>Category Management</h1>
          <p className="text-muted-foreground">Manage product categories</p>
        </div>
        <Button variant="primary">
          <Plus className="h-5 w-5" />
          Add Category
        </Button>
      </div>

      {/* Add Category Form */}
      <Card className="mb-6">
        <CardContent className="p-6">
          <h3 className="mb-4">Add New Category</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="text-sm mb-2 block">Category Name</label>
              <Input type="text" placeholder="e.g., Vegetables" />
            </div>
            <div>
              <label className="text-sm mb-2 block">Icon (Emoji)</label>
              <Input type="text" placeholder="e.g., 🥬" maxLength={2} />
            </div>
            <div className="flex items-end">
              <Button variant="primary" className="w-full">
                Add Category
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Categories Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {categories.map((category) => (
          <Card key={category.id}>
            <CardContent className="p-6">
              <div className="text-center mb-4">
                <div className="text-5xl mb-3">{category.icon}</div>
                <h4>{category.name}</h4>
              </div>
              <div className="flex gap-2">
                <Button variant="outline" size="sm" className="flex-1">
                  <Edit className="h-4 w-4" />
                </Button>
                <Button
                  variant="ghost"
                  size="sm"
                  className="flex-1 text-destructive"
                  onClick={() => handleDelete(category.name)}
                >
                  <Trash2 className="h-4 w-4" />
                </Button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
