# Meireles Connect — Product Requirements Doc

## Original problem statement (Portuguese)
"Analisa o documento, tem tudo o que e precise de funcionalidades, o codigo tem de ser feito exculsivamente em php, html, CSS e javaScript"

The attached document `LSIS1_25_26_enunciado_V_1.0.pdf` describes the **Meireles Connect** post-sales portal for Fogões Meireles (Portuguese appliance company). The portal must centralize catalog browsing, purchases, warranty management, technical assistance, and back-office for 4 user roles.

## User Choices (collected via ask_human)
- Database: **MySQL/MariaDB**
- MVP scope: **Full** (catalog + purchases + warranties + service + back-office + reports)
- Payment: **simulated**
- ERP SAGE integration: **mocked**
- Design: **modern & elegant**

## Stack (per constraint)
- Backend: **PHP 8.2** (pure, no framework)
- Database: **MariaDB 10.11**
- Frontend: **vanilla HTML/CSS/JavaScript** (no React, no Tailwind)
- Icons: Phosphor Icons via CDN
- Fonts: Inter + Outfit (Google Fonts)
- Served by PHP built-in server on port 3000 (replacing default React frontend via supervisor)

## User Personas
1. **Consumer (consumer)** — end customer, browses catalog, buys, activates warranties, opens service tickets
2. **Store Manager (store_manager)** — supports clients, views orders/tickets
3. **CAT Technician (cat)** — manages assigned tickets, performs diagnosis/intervention
4. **Administrator (admin)** — global oversight, user/product management, ticket assignment, reports

## Implemented Features (2026-06-15)
- ✅ Public catalog with filters (category, brand, price, search)
- ✅ Product detail page with specs, related parts
- ✅ Parts catalog with search
- ✅ Cart (session-based) with add/update/remove
- ✅ Multi-step checkout with simulated payment
- ✅ User authentication (login/register/logout, CSRF, bcrypt, session)
- ✅ Role-based redirects after login
- ✅ Consumer dashboard: orders, order detail with step tracker, warranties (with file upload), service tickets (with photo upload), notifications
- ✅ Store Manager dashboard with KPIs, orders, tickets
- ✅ CAT dashboard with KPIs, assigned tickets, diagnosis/intervention form
- ✅ Admin dashboard with KPIs, revenue bar chart, recent activity
- ✅ Admin user management (CRUD with modal)
- ✅ Admin product management (CRUD with modal)
- ✅ Admin order status update
- ✅ Admin ticket assignment to CAT
- ✅ Reports page: revenue by month/category, tickets by state, CAT performance
- ✅ CSV exports (orders, tickets, users)
- ✅ App settings (warranty alert days, shipping flat cost)
- ✅ Notification log (in-app simulated "emails")
- ✅ Activity logs (server-side)
- ✅ Mocked ERP SAGE sync endpoint
- ✅ Seeded demo data: 4 users, 5 categories, 8 products, 4 parts, 3 orders, 2 warranties, 3 tickets, 2 notifications

## Database
- `users`, `stores`, `cats`, `categories`, `products`, `parts`
- `orders`, `order_items`, `warranties`, `service_tickets`, `notifications`, `logs`, `settings`

## Seed Credentials
See `/app/memory/test_credentials.md`

## Backlog (deferred)
- **P1**: Real Stripe payment, real SMTP email sending, email-based password reset, multi-language (PT-EN)
- **P2**: Two-factor authentication, real-time updates via SSE/WebSocket, PDF invoice generation, multi-image product gallery, customer reviews
- **P2**: Real SAGE ERP integration with API key
- **P3**: Mobile app, AI-powered product recommendations, chatbot for support

## Notes
- The K8s ingress sends non-`/api/*` requests to port 3000. Because PHP serves the whole app on port 3000, no path collides with `/api/*`. The FastAPI backend on port 8001 is dormant.
