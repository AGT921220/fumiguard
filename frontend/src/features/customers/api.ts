import { api } from '../../app/api'

export type Customer = {
  id: number
  name: string
  email?: string | null
  phone?: string | null
  notes?: string | null
}

export type Site = {
  id: number
  customer_id: number
  name: string
  address_line1?: string | null
  address_line2?: string | null
  city?: string | null
  state?: string | null
  postal_code?: string | null
  country?: string | null
  lat?: string | null
  lng?: string | null
  notes?: string | null
}

export function listCustomers() {
  return api<Customer[]>('/api/v1/customers')
}

export function createCustomer(data: Partial<Customer> & { name: string }) {
  return api<Customer>('/api/v1/customers', { method: 'POST', json: data })
}

export function updateCustomer(id: number, data: Partial<Customer>) {
  return api<Customer>(`/api/v1/customers/${id}`, { method: 'PATCH', json: data })
}

export function deleteCustomer(id: number) {
  return api<{ ok: boolean }>(`/api/v1/customers/${id}`, { method: 'DELETE' })
}

export function listSites(customerId: number) {
  return api<Site[]>(`/api/v1/customers/${customerId}/sites`)
}

export function createSite(data: Partial<Site> & { customer_id: number; name: string }) {
  return api<Site>('/api/v1/sites', { method: 'POST', json: data })
}

