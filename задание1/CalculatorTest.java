import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.*;

@DisplayName("Тесты для калькулятора")
public class CalculatorTest {

    private final Calculator calculator = new Calculator();

    @Test
    @DisplayName("1. Сложение положительных чисел")
    public void testAddPositive() {
        assertEquals(5, calculator.add(2, 3), "2 + 3 должно быть 5");
        assertEquals(10, calculator.add(7, 3), "7 + 3 должно быть 10");
    }

    @Test
    @DisplayName("2. Сложение отрицательных чисел")
    public void testAddNegative() {
        assertEquals(-5, calculator.add(-2, -3), "-2 + -3 должно быть -5");
        assertEquals(0, calculator.add(-5, 5), "-5 + 5 должно быть 0");
    }

    @Test
    @DisplayName("3. Вычитание положительных чисел")
    public void testSubtractPositive() {
        assertEquals(3, calculator.subtract(5, 2), "5 - 2 должно быть 3");
        assertEquals(0, calculator.subtract(5, 5), "5 - 5 должно быть 0");
    }

    @Test
    @DisplayName("4. Вычитание отрицательных чисел")
    public void testSubtractNegative() {
        assertEquals(-3, calculator.subtract(-5, -2), "-5 - -2 должно быть -3");
        assertEquals(-7, calculator.subtract(-5, 2), "-5 - 2 должно быть -7");
    }

    @Test
    @DisplayName("5. Умножение положительных чисел")
    public void testMultiplyPositive() {
        assertEquals(6, calculator.multiply(2, 3), "2 * 3 должно быть 6");
        assertEquals(0, calculator.multiply(0, 5), "0 * 5 должно быть 0");
    }

    @Test
    @DisplayName("6. Умножение на ноль")
    public void testMultiplyByZero() {
        assertEquals(0, calculator.multiply(5, 0), "5 * 0 должно быть 0");
        assertEquals(0, calculator.multiply(0, 5), "0 * 5 должно быть 0");
    }

    @Test
    @DisplayName("7. Деление положительных чисел")
    public void testDividePositive() {
        assertEquals(2, calculator.divide(6, 3), "6 / 3 должно быть 2");
        assertEquals(3, calculator.divide(9, 3), "9 / 3 должно быть 3");
    }

    @Test
    @DisplayName("8. Деление на ноль (исключение)")
    public void testDivideByZero() {
        Exception exception = assertThrows(IllegalArgumentException.class, () -> {
            calculator.divide(5, 0);
        });
        assertEquals("Division by zero is not allowed", exception.getMessage());
    }
}