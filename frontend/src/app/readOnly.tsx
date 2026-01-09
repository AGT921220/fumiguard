import React, { createContext, useContext, useMemo, useState } from 'react'

type ReadOnlyContextValue = {
  isReadOnly: boolean
  setReadOnly: (value: boolean) => void
}

const Ctx = createContext<ReadOnlyContextValue | null>(null)

export function ReadOnlyProvider({ children }: { children: React.ReactNode }) {
  const [isReadOnly, setReadOnly] = useState(false)

  const value = useMemo(
    () => ({ isReadOnly, setReadOnly }),
    [isReadOnly],
  )

  return <Ctx.Provider value={value}>{children}</Ctx.Provider>
}

export function useReadOnly() {
  const ctx = useContext(Ctx)
  if (!ctx) throw new Error('useReadOnly must be used within ReadOnlyProvider')
  return ctx
}

