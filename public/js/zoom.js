class ZoomIntegration {
    constructor() {
        this.apiUrl = '/external-apps/zoom';
    }

    async createMeeting(courseId) {
        const response = await fetch(this.apiUrl + '/create-meeting', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ course_id: courseId })
        });

        return await response.json();
    }

    async showMeeting(meetingId) {
        const response = await fetch(`${this.apiUrl}/meeting/${meetingId}`);
        return await response.text();
    }
}