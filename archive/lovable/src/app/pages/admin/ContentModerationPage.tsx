import { Card, CardContent, CardHeader, CardTitle } from '../../components/ui/Card';
import { Button } from '../../components/ui/Button';
import { Badge } from '../../components/ui/Badge';
import { Flag, Check, X } from 'lucide-react';
import { toast } from 'sonner';

export function ContentModerationPage() {
  const reportedContent = [
    {
      id: '1',
      type: 'review',
      content: 'This product is terrible and overpriced!',
      reporter: 'John Doe',
      reported: 'Jane Smith',
      date: '2026-05-08',
      reason: 'Inappropriate language',
    },
    {
      id: '2',
      type: 'recipe',
      content: 'Spicy Chicken Wings Recipe',
      reporter: 'Sarah Johnson',
      reported: 'Mike Chen',
      date: '2026-05-07',
      reason: 'Duplicate content',
    },
    {
      id: '3',
      type: 'product',
      content: 'Fresh Organic Tomatoes',
      reporter: 'Emma Wilson',
      reported: 'Green Valley Store',
      date: '2026-05-06',
      reason: 'Misleading description',
    },
  ];

  const handleApprove = (contentId: string) => {
    toast.success('Content approved');
  };

  const handleRemove = (contentId: string) => {
    toast.error('Content removed');
  };

  return (
    <div>
      <div className="mb-8">
        <h1>Content Moderation</h1>
        <p className="text-muted-foreground">Review reported content and take action</p>
      </div>

      {/* Summary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <Card>
          <CardContent className="p-6 text-center">
            <div className="text-3xl mb-2">12</div>
            <div className="text-sm text-muted-foreground">Pending Reports</div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6 text-center">
            <div className="text-3xl mb-2">45</div>
            <div className="text-sm text-muted-foreground">Resolved This Week</div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6 text-center">
            <div className="text-3xl mb-2">8</div>
            <div className="text-sm text-muted-foreground">Content Removed</div>
          </CardContent>
        </Card>
      </div>

      {/* Reported Content */}
      <div className="space-y-4">
        {reportedContent.map((report) => (
          <Card key={report.id}>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center gap-2">
                  <Flag className="h-5 w-5 text-[var(--orange-accent)]" />
                  Reported {report.type}
                </CardTitle>
                <Badge variant="warning">Pending Review</Badge>
              </div>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <div className="text-sm text-muted-foreground mb-1">Content</div>
                  <div className="p-3 bg-secondary rounded">{report.content}</div>
                </div>
                <div className="space-y-2">
                  <div>
                    <div className="text-sm text-muted-foreground">Reporter</div>
                    <div>{report.reporter}</div>
                  </div>
                  <div>
                    <div className="text-sm text-muted-foreground">Reported User</div>
                    <div>{report.reported}</div>
                  </div>
                  <div>
                    <div className="text-sm text-muted-foreground">Reason</div>
                    <div>{report.reason}</div>
                  </div>
                  <div>
                    <div className="text-sm text-muted-foreground">Date</div>
                    <div>{report.date}</div>
                  </div>
                </div>
              </div>
              <div className="flex gap-3">
                <Button
                  variant="primary"
                  onClick={() => handleApprove(report.id)}
                >
                  <Check className="h-4 w-4" />
                  Approve Content
                </Button>
                <Button
                  variant="outline"
                  onClick={() => handleRemove(report.id)}
                  className="text-destructive"
                >
                  <X className="h-4 w-4" />
                  Remove Content
                </Button>
                <Button variant="ghost">
                  Warn User
                </Button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
