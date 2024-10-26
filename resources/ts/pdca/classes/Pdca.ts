export class Todo {
    constructor(
        public id: number,
        public title: string,
        public description: string | null,
        public due: string | null,
        public when_completed: Date | null,
        public progress_rate: string | null,
        public priority: string | null,
        public label: string | null,
        public deleted_at: Date | null,
    ) {}
}
