<?php
$changes = $changes ?? [];
$changeClass = static function (float $value): string {
  if ($value > 0) return 'up';
  if ($value < 0) return 'down';
  return 'neutral';
};
$changeText = static function (float $value): string {
  if ($value > 0) return '▲ ' . number_format(abs($value), 1) . '% vs previous 30 days';
  if ($value < 0) return '▼ ' . number_format(abs($value), 1) . '% vs previous 30 days';
  return '• No change vs previous 30 days';
};
?>

<div class="dashboard-hero card">
  <div>
    <p class="dashboard-eyebrow">Platform analytics</p>
    <h2>Admin dashboard</h2>
    <p class="text-muted dashboard-intro">Track platform growth, merchant activity, revenue movement, and category mix from one overview.</p>
  </div>
</div>
<div class="stat-grid">
  <div class="stat">
    <div class="num"><?= (int)$stats['users'] ?></div>
    <div class="label">Platform users</div>
    <div class="indicator <?= $changeClass((float) ($changes['users'] ?? 0)) ?>"><?= $changeText((float) ($changes['users'] ?? 0)) ?></div>
  </div>
  <div class="stat">
    <div class="num"><?= (int)$stats['active_merchants'] ?></div>
    <div class="label">Active merchants</div>
    <div class="indicator <?= $changeClass((float) ($changes['active_merchants'] ?? 0)) ?>"><?= $changeText((float) ($changes['active_merchants'] ?? 0)) ?></div>
  </div>
  <div class="stat">
    <div class="num">RM <?= number_format((float)$stats['revenue'], 2) ?></div>
    <div class="label">Platform revenue</div>
    <div class="indicator <?= $changeClass((float) ($changes['revenue'] ?? 0)) ?>"><?= $changeText((float) ($changes['revenue'] ?? 0)) ?></div>
  </div>
  <div class="stat">
    <div class="num"><?= (int)$stats['orders'] ?></div>
    <div class="label">Orders placed</div>
    <div class="indicator <?= $changeClass((float) ($changes['orders'] ?? 0)) ?>"><?= $changeText((float) ($changes['orders'] ?? 0)) ?></div>
  </div>
</div>

<div class="stat-grid admin-detail-grid">
  <div class="stat"><div class="num"><?= (int)$stats['pending'] ?></div><div class="label">Pending stores</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['products'] ?></div><div class="label">Active products</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['reports'] ?></div><div class="label">Open reports</div></div>
</div>

<div class="chart-grid">
  <section class="card chart-card chart-card-span-2">
    <div class="chart-card-header">
      <div>
        <p class="chart-kicker">Growth</p>
        <h3>Platform growth comparison</h3>
        <p class="text-muted">Cumulative growth across the last 6 months for users and active merchants.</p>
      </div>
    </div>
    <div class="d3-chart" data-chart="multi-line" data-chart-series='<?= htmlspecialchars(json_encode($growthChartSeries ?? []), ENT_QUOTES) ?>'></div>
  </section>
  <section class="card chart-card chart-card-bar">
    <div class="chart-card-header">
      <div>
        <p class="chart-kicker">Revenue</p>
        <h3>Monthly Revenue</h3>
        <p class="text-muted">Combined monthly revenue across all merchants based on paid merchant orders.</p>
      </div>
    </div>
    <div class="d3-chart" data-chart="bar" data-value-prefix="RM " data-chart-step="100" data-chart-height="300" data-chart-values='<?= htmlspecialchars(json_encode($revenueChart ?? []), ENT_QUOTES) ?>'></div>
  </section>
  <section class="card chart-card chart-card-pie">
    <div class="chart-card-header">
      <div>
        <p class="chart-kicker">Catalog mix</p>
        <h3>Product categories</h3>
        <p class="text-muted">Distribution of active products across marketplace categories.</p>
      </div>
    </div>
    <div class="d3-chart" data-chart="pie" data-chart-values='<?= htmlspecialchars(json_encode($categoryChart ?? []), ENT_QUOTES) ?>'></div>
  </section>
</div>
