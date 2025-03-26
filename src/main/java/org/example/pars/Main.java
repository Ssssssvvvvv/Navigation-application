package org.example.pars;
import java.io.IOException;
import java.util.Arrays;
import java.util.List;

public class Main {

    private static final String[] rooms = new String[]{"302", "203", "204", "214", "336", "425", "214", "435"};
    private static final List<String> roomsList= Arrays.asList(rooms);

    public static void main(String[] args) throws IOException {
        String targetRoom = "302";
        if (!roomsList.contains(targetRoom)){
            System.out.println("Аудитория не является учебной");
        }
        else {
            UniversityParser parser = new UniversityParser();
            List<Lesson> lessonsToday = parser.getLessonsForToday(targetRoom);

            if (lessonsToday.isEmpty()) {
                System.out.println("Сегодня пар в аудитории " + targetRoom + " нет.");
            } else {
                for (Lesson lesson : lessonsToday) {
                    System.out.println("\n" + lesson);
                }
            }
        }
    }
}