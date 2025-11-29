
export const SPEED_SLOW = 'slow'
export const SPEED_MEDIUM = 'medium'
export const SPEED_FAST = 'fast'

export const SPEED_LABELS = {
    [SPEED_SLOW]: 'Медленно',
    [SPEED_MEDIUM]: 'Среднее',
    [SPEED_FAST]: 'Быстро'
}

export const SPEED_OPTIONS = [
    { label: SPEED_LABELS[SPEED_SLOW], value: SPEED_SLOW },
    { label: SPEED_LABELS[SPEED_MEDIUM], value: SPEED_MEDIUM },
    { label: SPEED_LABELS[SPEED_FAST], value: SPEED_FAST }
]