<?php use App\Helpers\Csrf; ?>
<?php use App\Helpers\AuthHelper; ?>
<div class="product-detail">
  <div class="gallery">
    <?php if (!empty($product['image'])): ?>
      <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($product['image']) ?>"
        alt="<?= htmlspecialchars($product['product_name']) ?>">
    <?php else: ?>🥗<?php endif; ?>
  </div>
  <div>
    <h2><?= htmlspecialchars($product['product_name']) ?></h2>
    <p class="text-muted">
      <a href="<?= BASE_URL ?>/stores/<?= (int) $product['store_id'] ?>"><?= htmlspecialchars($product['store_name']) ?></a>
      · <?= htmlspecialchars($product['category_name'] ?? '') ?>
    </p>
    <p><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
    <p><strong>Price:</strong> RM <?= number_format((float) $product['price'], 2) ?></p>
    <p><strong>Package:</strong> <?= number_format((float) $product['package_quantity'], 0) ?>
      <?= htmlspecialchars($product['package_unit']) ?>
    </p>
    <p><strong>Stock:</strong> <?= (int) $product['stock_quantity'] ?> available</p>
    <p><strong>Rating:</strong> <?= number_format((float) $product['rating'], 1) ?> ★</p>

    <?php if ($product['status'] === 'active' && (int) $product['stock_quantity'] > 0): ?>
      <form method="post" action="<?= BASE_URL ?>/cart/add" class="flex mt-2">
        <?= Csrf::field() ?>
        <input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>">
        <div data-stepper class="flex">
          <button type="button" class="btn btn-outline btn-sm" data-step="-1">−</button>
          <input type="number" name="quantity" value="1" min="1" max="<?= (int) $product['stock_quantity'] ?>"
            style="width:80px">
          <button type="button" class="btn btn-outline btn-sm" data-step="1">+</button>
        </div>
        <button class="btn btn-primary">Add to cart</button>
      </form>
    <?php else: ?>
      <p class="badge badge-danger">Unavailable</p>
    <?php endif; ?>
  </div>
</div>

<section class="mt-3">
  <h3>Reviews</h3>
  <?php if (!$reviews): ?>
    <p class="text-muted">No reviews yet.</p>
  <?php else:
    foreach ($reviews as $r): ?>
      <div class="card">
        <strong><?= htmlspecialchars($r['username']) ?></strong>
        <span class="badge badge-success"><?= (int) $r['rating'] ?> ★</span>
        <?php if (trim((string) ($r['comment'] ?? '')) !== ''): ?>
          <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
        <?php endif; ?>
      </div>
    <?php endforeach; endif; ?>
</section>

<?php if (AuthHelper::check()): ?>
  <section class="mt-3 grid grid-2">
    <?php $currentUserReview = $currentUserReview ?? null; ?>
    <form method="post" action="<?= BASE_URL ?>/products/<?= (int) $product['product_id'] ?>/reviews" class="card">
      <?= Csrf::field() ?>
      <h3><?= $currentUserReview ? 'Edit your review' : 'Write a review' ?></h3>
      <label>Rating</label>
      <select name="rating">
        <?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
          <option value="<?= $rating ?>" <?= (int) ($currentUserReview['rating'] ?? 5) === $rating ? 'selected' : '' ?>><?= $rating ?> ★</option>
        <?php endforeach; ?>
      </select>
      <label>Comment</label>
      <textarea name="comment"><?= htmlspecialchars($currentUserReview['comment'] ?? '') ?></textarea>
      <button class="btn btn-primary mt-1"><?= $currentUserReview ? 'Update review' : 'Submit review' ?></button>
    </form>

    <form method="post" action="<?= BASE_URL ?>/reports" class="card">
      <?= Csrf::field() ?>
      <h3>Report product</h3>
      <input type="hidden" name="target_type" value="product">
      <input type="hidden" name="target_id" value="<?= (int) $product['product_id'] ?>">
      <label>Reason</label>
      <textarea name="reason" required></textarea>
      <button class="btn btn-outline mt-1">Submit report</button>
    </form>
  </section>
<?php endif; ?>
