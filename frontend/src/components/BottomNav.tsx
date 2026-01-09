import { NavLink } from 'react-router-dom'

const items = [
  { to: '/dashboard', label: 'Inicio' },
  { to: '/customers', label: 'Clientes' },
  { to: '/agenda', label: 'Agenda' },
  { to: '/work-orders', label: 'Órdenes' },
  { to: '/billing', label: 'Billing' },
]

export function BottomNav() {
  return (
    <div className="fixed bottom-0 left-0 right-0 z-20 border-t border-slate-200 bg-white">
      <div className="mx-auto flex max-w-5xl justify-between px-2 py-2">
        {items.map((i) => (
          <NavLink
            key={i.to}
            to={i.to}
            className={({ isActive }) =>
              `flex w-full flex-col items-center rounded-lg px-2 py-2 text-xs ${
                isActive ? 'bg-slate-100 font-semibold text-slate-900' : 'text-slate-600'
              }`
            }
          >
            {i.label}
          </NavLink>
        ))}
      </div>
    </div>
  )
}

