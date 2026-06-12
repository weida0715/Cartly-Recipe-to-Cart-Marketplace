import { createBrowserRouter } from "react-router";
import { CustomerLayout } from "./layouts/CustomerLayout";
import { ProtectedMerchantLayout } from "./layouts/ProtectedMerchantLayout";
import { ProtectedAdminLayout } from "./layouts/ProtectedAdminLayout";

// Customer Pages
import { HomePage } from "./pages/customer/HomePage";
import { LoginPage } from "./pages/auth/LoginPage";
import { RegisterPage } from "./pages/auth/RegisterPage";
import { ForgotPasswordPage } from "./pages/auth/ForgotPasswordPage";
import { CustomerDashboard } from "./pages/customer/CustomerDashboard";
import { MarketplacePage } from "./pages/customer/MarketplacePage";
import { ProductDetailsPage } from "./pages/customer/ProductDetailsPage";
import { RecipeDiscoveryPage } from "./pages/customer/RecipeDiscoveryPage";
import { RecipeDetailsPage } from "./pages/customer/RecipeDetailsPage";
import { CartPage } from "./pages/customer/CartPage";
import { CheckoutPage } from "./pages/customer/CheckoutPage";
import { OrderConfirmationPage } from "./pages/customer/OrderConfirmationPage";
import { OrderHistoryPage } from "./pages/customer/OrderHistoryPage";

// Merchant Pages
import { MerchantDashboard } from "./pages/merchant/MerchantDashboard";
import { ProductManagementPage } from "./pages/merchant/ProductManagementPage";
import { MerchantOrdersPage } from "./pages/merchant/MerchantOrdersPage";
import { VoucherManagementPage } from "./pages/merchant/VoucherManagementPage";
import { StoreProfilePage } from "./pages/merchant/StoreProfilePage";

// Admin Pages
import { AdminDashboard } from "./pages/admin/AdminDashboard";
import { MerchantApprovalPage } from "./pages/admin/MerchantApprovalPage";
import { UserManagementPage } from "./pages/admin/UserManagementPage";
import { CategoryManagementPage } from "./pages/admin/CategoryManagementPage";
import { ContentModerationPage } from "./pages/admin/ContentModerationPage";

import { NotFoundPage } from "./pages/NotFoundPage";

export const router = createBrowserRouter([
  {
    path: "/",
    Component: CustomerLayout,
    children: [
      { index: true, Component: HomePage },
      { path: "marketplace", Component: MarketplacePage },
      { path: "product/:id", Component: ProductDetailsPage },
      { path: "recipes", Component: RecipeDiscoveryPage },
      { path: "recipe/:id", Component: RecipeDetailsPage },
      { path: "cart", Component: CartPage },
      { path: "checkout", Component: CheckoutPage },
      { path: "order-confirmation/:id", Component: OrderConfirmationPage },
      { path: "dashboard", Component: CustomerDashboard },
      { path: "orders", Component: OrderHistoryPage },
    ],
  },
  {
    path: "/auth",
    children: [
      { path: "login", Component: LoginPage },
      { path: "register", Component: RegisterPage },
      { path: "forgot-password", Component: ForgotPasswordPage },
    ],
  },
  {
    path: "/merchant",
    Component: ProtectedMerchantLayout,
    children: [
      { index: true, Component: MerchantDashboard },
      { path: "products", Component: ProductManagementPage },
      { path: "orders", Component: MerchantOrdersPage },
      { path: "vouchers", Component: VoucherManagementPage },
      { path: "store", Component: StoreProfilePage },
    ],
  },
  {
    path: "/admin",
    Component: ProtectedAdminLayout,
    children: [
      { index: true, Component: AdminDashboard },
      { path: "merchants", Component: MerchantApprovalPage },
      { path: "users", Component: UserManagementPage },
      { path: "categories", Component: CategoryManagementPage },
      { path: "moderation", Component: ContentModerationPage },
    ],
  },
  {
    path: "*",
    Component: NotFoundPage,
  },
]);
