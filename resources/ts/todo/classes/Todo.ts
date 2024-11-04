export class GoogleUser {
    constructor(
        public id: number,
        public access_token: string,
        public refresh_token: string,
    ) {}
}

export class User {
    public google_user: GoogleUser | null;

    constructor(
        public id: number,
        public name: string,
        google_user: GoogleUser | null,
    ) {
        this.google_user = google_user;
    }
}

export class Todo {
    public google_user: GoogleUser | null;
    public user: User;

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
        public event_id: string | null,
        google_user: GoogleUser | null,
        user: User,
    ) {
        this.google_user = google_user;
        this.user = user;
    }
}
