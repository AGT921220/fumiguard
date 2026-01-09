import { Navigate, Route, Routes } from 'react-router-dom'
import { ProtectedRoute } from './routes/ProtectedRoute'
import { AppLayout } from './routes/AppLayout'
import { LoginPage } from './features/auth/LoginPage'
import { DashboardPage } from './features/dashboard/DashboardPage'
import { CustomersPage } from './features/customers/CustomersPage'
import { CustomerDetailPage } from './features/customers/CustomerDetailPage'
import { ServicePlansPage } from './features/servicePlans/ServicePlansPage'
import { AgendaPage } from './features/agenda/AgendaPage'
import { WorkOrdersPage } from './features/workOrders/WorkOrdersPage'
import { ExecutorPage } from './features/executor/ExecutorPage'
import { ReportsPage } from './features/reports/ReportsPage'
import { BillingPage } from './features/billing/BillingPage'

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />

      <Route element={<ProtectedRoute />}>
        <Route element={<AppLayout />}>
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="/dashboard" element={<DashboardPage />} />
          <Route path="/customers" element={<CustomersPage />} />
          <Route path="/customers/:id" element={<CustomerDetailPage />} />
          <Route path="/service-plans" element={<ServicePlansPage />} />
          <Route path="/agenda" element={<AgendaPage />} />
          <Route path="/work-orders" element={<WorkOrdersPage />} />
          <Route path="/executor/:workOrderId" element={<ExecutorPage />} />
          <Route path="/reports" element={<ReportsPage />} />
          <Route path="/billing" element={<BillingPage />} />
        </Route>
      </Route>

      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}
