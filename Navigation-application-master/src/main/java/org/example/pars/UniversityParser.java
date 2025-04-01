package org.example.pars;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.*;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

public class UniversityParser {
    private static final String BASE_URL = "https://rasp.sstu.ru";

    public List<Lesson> getLessonsForToday(String targetRoom) throws IOException {
        String todayDate = new SimpleDateFormat("dd.MM").format(new Date());
        List<Lesson> lessonsToday = new ArrayList<>();

        Document roomsPage = Jsoup.connect(BASE_URL + "/rasp/rooms").get();

        Elements buildingSections = roomsPage.select("div.list-abc");

        Element fifthBuildingSection = null;
        for (Element section : buildingSections) {
            if (section.text().equals("5 корпус")) {
                fifthBuildingSection = section;
                break;
            }
        }

        Element roomListSection = fifthBuildingSection.nextElementSibling();

        Elements roomLinks = roomListSection.select("ul li a");
        String roomScheduleUrl = null;

        for (Element link : roomLinks) {
            if (link.text().equals(targetRoom)) {
                roomScheduleUrl = BASE_URL + link.attr("href");
                break;
            }
        }

        if (roomScheduleUrl != null) {

            Document schedulePage = Jsoup.connect(roomScheduleUrl).get();
            Elements days = schedulePage.select(".day");

            for (Element day : days) {
                String dayHeader = day.select(".day-header").text();
                if (dayHeader.contains(todayDate)) {
                    Elements lessons = day.select(".day-lesson");

                    for (Element lesson : lessons) {
                        String room = lesson.select(".lesson-room").text();
                        if (room.contains(targetRoom)) {
                            String time = lesson.select(".lesson-hour").text();
                            String subject = lesson.select(".lesson-name").text();
                            String teacher = lesson.select(".lesson-teacher").text();

                            Lesson newLesson = new Lesson(time, room, subject, teacher);
                            lessonsToday.add(newLesson);
                        }
                    }
                }
            }
        }
        else {
            System.out.println("Аудитория " + targetRoom + " не найдена в 5 корпусе.");
        }

        return lessonsToday;
    }
}