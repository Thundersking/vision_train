

export const DIFFICULTY_EASY = 'easy'
export const DIFFICULTY_MEDIUM = 'medium'
export const DIFFICULTY_HARD = 'hard'

export const DIFFICULTY_LABELS = {
    [DIFFICULTY_EASY]: 'Легкий',
    [DIFFICULTY_MEDIUM]: 'Средний',
    [DIFFICULTY_HARD]: 'Сложный'
}

export const DIFFICULTY_OPTIONS = [
    { label: DIFFICULTY_LABELS[DIFFICULTY_EASY], value: DIFFICULTY_EASY },
    { label: DIFFICULTY_LABELS[DIFFICULTY_MEDIUM], value: DIFFICULTY_MEDIUM },
    { label: DIFFICULTY_LABELS[DIFFICULTY_HARD], value: DIFFICULTY_HARD }
]