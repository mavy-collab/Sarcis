function taskManager() {
    return {
        tasks: [],
        counter: 0,
        addTask() {
            this.tasks.push({
                id: this.counter++,
                title: '',
                priority: 'medium',
                status: 'todo',
                due_date: '',
            });
        },
        removeTask(index) {
            this.tasks.splice(index, 1);
        }
    }
}
