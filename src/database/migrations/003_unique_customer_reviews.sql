-- Ensure each customer can only have one review per product or recipe.
ALTER TABLE reviews
  ADD UNIQUE KEY uniq_user_product_review (user_id, product_id),
  ADD UNIQUE KEY uniq_user_recipe_review (user_id, recipe_id);
