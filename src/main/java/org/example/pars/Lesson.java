package org.example.pars;

public class Lesson {
    String time;
    String room;
    String subject;
    String teacher;

    public Lesson(String time, String room, String subject, String teacher) {
        this.time = time;
        this.room = room;
        this.subject = subject;
        this.teacher = teacher;
    }

    public String toString() {
        return "Время: " + time.substring(0, time.length() - 1) + "\nПредмет: " + subject + "\nПреподаватель: " + teacher;
    }

    public int getStartHour() {
        String[] times = time.split(" - ");
        String startTime = times[0];
        String[] hour = startTime.split(":");
        return Integer.parseInt(hour[0]);
    }


}