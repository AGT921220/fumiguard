import { api } from '../../app/api'
import { showError } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { useBillingStatus } from './useBillingStatus'

export function BillingPage() {
  const { data, isLoading } = useBillingStatus()

  async function startCheckout(plan_key: 'BASIC' | 'PRO' | 'ENTERPRISE') {
    try {
      const res = await api<{ url: string }>('/api/v1/billing/checkout', {
        method: 'POST',
        json: { plan_key },
      })
      window.location.href = res.url
    } catch (e) {
      showError(e, 'No se pudo abrir checkout')
    }
  }

  async function openPortal() {
    try {
      const res = await api<{ url: string }>('/api/v1/billing/portal', {
        method: 'POST',
      })
      window.location.href = res.url
    } catch (e) {
      showError(e, 'No se pudo abrir portal')
    }
  }

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <h1 className="text-xl font-bold tracking-tight">Billing</h1>
      <p className="mt-1 text-sm text-slate-600">
        Estado de suscripción y gestión en Stripe.
      </p>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="text-sm">
            <div className="font-semibold">Estado</div>
            <div className="mt-1 text-slate-700">
              {isLoading ? 'Cargando…' : data ? `${data.plan_key} · ${data.status}` : '—'}
            </div>
            {!data ? (
              <div className="mt-2 text-xs text-slate-500">
                Nota: el backend aún no expone `/billing/status` (la UI sigue operando).
              </div>
            ) : null}
          </div>
          <div className="mt-4 flex flex-col gap-2">
            <Button onClick={() => startCheckout('BASIC')}>Suscribirme BASIC</Button>
            <Button onClick={() => startCheckout('PRO')} className="bg-indigo-600">
              Suscribirme PRO
            </Button>
            <Button onClick={() => startCheckout('ENTERPRISE')} className="bg-emerald-600">
              Suscribirme ENTERPRISE
            </Button>
            <Button onClick={openPortal} className="bg-slate-100 text-slate-900">
              Abrir portal (Stripe)
            </Button>
          </div>
        </Card>
      </div>
    </div>
  )
}

