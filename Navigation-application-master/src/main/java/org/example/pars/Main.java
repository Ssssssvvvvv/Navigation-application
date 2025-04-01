package org.example.pars;

import java.sql.*;
import java.util.List;
import java.util.Properties;
import java.io.InputStream;
import java.io.IOException;

public class Main {
    private static final String CONFIG_FILE = "config.properties";

    public static void main(String[] args) {
        try {
            Properties config = loadConfig();
            String targetRoom = "102"; // Можно заменить на аргумент командной строки

            String roomType = getRoomTypeFromDatabase(config, targetRoom);

            if (roomType == null) {
                System.out.println("Комната " + targetRoom + " не найдена в базе данных.");
            } else if (!"лекционная".equalsIgnoreCase(roomType) || !"компьютерная".equalsIgnoreCase(roomType) ||
            !"раздевалка".equalsIgnoreCase(roomType) || !"кафедра".equalsIgnoreCase(roomType) || !"деканат".equalsIgnoreCase(roomType)) {
                System.out.println("Аудитория " + targetRoom + " не является учебной (тип: " + roomType + ").");
            } else {
                UniversityParser parser = new UniversityParser();
                List<Lesson> lessonsToday = parser.getLessonsForToday(targetRoom);

                if (lessonsToday.isEmpty()) {
                    System.out.println("Сегодня пар в аудитории " + targetRoom + " нет.");
                } else {
                    System.out.println("\nРасписание для аудитории " + targetRoom + ":");
                    lessonsToday.forEach(System.out::println);
                }
            }
        } catch (SQLException e) {
            System.err.println("Ошибка базы данных: " + e.getMessage());
            e.printStackTrace();
        } catch (IOException e) {
            System.err.println("Ошибка загрузки конфигурации: " + e.getMessage());
        } catch (Exception e) {
            System.err.println("Общая ошибка: " + e.getMessage());
        }
    }

    private static Properties loadConfig() throws IOException {
        Properties props = new Properties();
        try (InputStream input = Main.class.getClassLoader().getResourceAsStream(CONFIG_FILE)) {
            if (input == null) {
                throw new IOException("Файл конфигурации " + CONFIG_FILE + " не найден");
            }
            props.load(input);
        }
        return props;
    }

    private static String getRoomTypeFromDatabase(Properties config, String roomNumber) throws SQLException {
        String url = config.getProperty("db.url");
        String user = config.getProperty("db.user");
        String password = config.getProperty("db.password");

        try (Connection connection = DriverManager.getConnection(url, user, password);
             PreparedStatement statement = connection.prepareStatement(
                 "SELECT room_type FROM rooms WHERE room_number = ?")) {
            
            statement.setString(1, roomNumber);
            
            try (ResultSet resultSet = statement.executeQuery()) {
                return resultSet.next() ? resultSet.getString("room_type") : null;
            }
        }
    }
}